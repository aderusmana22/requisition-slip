<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFreeGoodsRequest;
use App\Http\Requests\UpdateFreeGoodsRequest;
use App\Jobs\sendFreeGoods;
use App\Mail\MailRejectFreeGoods;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\Requisition\Tracking;
use App\Models\User;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ApprovalTrait;
use App\Traits\traitTracking;
use Illuminate\Support\Str;
use App\Notifications\RequisitionNotification; // Pastikan ini di-import
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini di-import

class FreeGoodsController extends Controller
{
    use ApprovalTrait;
    use traitTracking;

    //======================================================================
    // PRIVATE HELPERS
    //======================================================================

    private function generateFgNumber()
    {
        // Format: FG YY MM XXX
        $prefix = 'FG';
        $year = date('y');
        $month = date('m');
        $currentPrefix = "$prefix $year $month";

        $lastRequisition = Requisition::where('category', 'FREE GOODS')
                                    ->where('no_srs', 'LIKE', $currentPrefix . ' %')
                                    ->orderBy('no_srs', 'desc')
                                    ->first();

        $runningNumber = 1;
        if ($lastRequisition) {
            $lastParts = explode(' ', $lastRequisition->no_srs);
            $lastRunningNumber = end($lastParts);
            $runningNumber = intval($lastRunningNumber) + 1;
        }

        return $currentPrefix . ' ' . sprintf('%03d', $runningNumber);
    }

    private function findUserForStep(string $stepName)
    {
        try {
            // Cari user berdasarkan nama yang tersimpan di current_position (Tracking Path)
            $user = User::where('name', $stepName)->first();

            if ($user) {
                return $user;
            }

            // Fallback khusus untuk supervisor gudang jika nama tidak exact
            if (str_contains($stepName, 'Outward WH Supervisor')) {
                $user = User::where('name', 'like', '%' . 'Outward WH Supervisor' . '%')->first();
                return $user ?: User::where('nik', 'WH0002')->first(); // Hardcode fallback jika perlu
            }

            Log::error("Tidak ada user yang ditemukan dengan NAMA '{$stepName}' untuk proses tracking Free Goods.");
            return null;

        } catch (\Exception $e) {
            Log::error("Error saat mencari user '{$stepName}': " . $e->getMessage());
            return null;
        }
    }

    //======================================================================
    // PUBLIC ENDPOINTS (Page Views & DataTables)
    //======================================================================

    public function getNextFgNumber()
    {
        return response()->json(['next_fg_number' => $this->generateFgNumber()]);
    }

    public function getAllItemMasters()
    {
        $masters = ItemMaster::select('id', 'item_master_code', 'item_master_name', 'unit')->get();
        return response()->json($masters);
    }

    public function index()
    {
        $customers = Customer::all();
        $generatedFg = $this->generateFgNumber();
        $user = Auth::user();
        $userDepartmentName = $user->department?->name ?? null;

        return view('page.freegoods.index', compact(
            'customers', 'generatedFg', 'userDepartmentName'));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->where('requisitions.category', 'FREE GOODS')
            ->select('requisitions.id', 'requisitions.no_srs', 'requisitions.requester_nik', 'requisitions.request_date', 'requisitions.created_at', 'requisitions.cost_center', 'requisitions.sub_category', 'requisitions.route_to', 'requisitions.status', 'users.name as requester_name', 'users.avatar', 'customers.name as customer_name');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('requisitions.status', $request->status);
        }

        if (!$user->hasRole('super-admin')) {
             $query->where('requisitions.requester_nik', $user->nik);
        }

        //$query->orderBy('requisitions.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('no_srs', fn($req) => $req->no_srs ? '<span class="badge-custom badge-fg-no"># ' . e($req->no_srs) . '</span>' : '-')
            ->addColumn('requester_info', fn($req) => '<div class="requester-badge"><i class="ph-bold ph-user-circle"></i><span>' . e($req->requester_name) . '</span></div>')
            ->editColumn('request_date', fn($req) => Carbon::parse($req->created_at)->format('d M Y, H:i'))
            ->editColumn('sub_category', fn($req) => '<span class="badge-custom badge-category">' . e($req->sub_category) . '</span>')
            ->editColumn('route_to', fn($req) => '<span class="badge-custom badge-route-to"><i class="ph-bold ph-user-switch me-1"></i>' . e($req->route_to) . '</span>')
            
            ->editColumn('status', function ($req) {
                $status = $req->status;
                $badgeClass = '';
                if (in_array($status, ['Submitted', 'Pending'])) {
                    $badgeClass = 'badge-status-pending';
                } elseif ($status === 'In Progress' || $status === 'Processing') {
                    $badgeClass = 'badge-status-progress';
                } elseif (in_array($status, ['Approved', 'Completed'])) {
                    $badgeClass = 'bg-success';
                } elseif (in_array($status, ['Rejected', 'Recalled'])) {
                    $badgeClass = 'bg-danger';
                }
                return '<span class="badge-custom ' . $badgeClass . '">' . e($status) . '</span>';
            })
            
            ->addColumn('action', function ($row) use ($user) {
                $viewBtn = '<button type="button" class="btn btn-info btn-sm action-btn-hover btn-view-requisition" data-id="' . $row->id . '" data-tooltip="View Details"><i class="ph-bold ph-eye"></i></button>';
                $recallBtn = '';
                $duplicateBtn = '';

                // Tombol Recall hanya jika Pending dan milik user login
                if ($row->status === 'Pending' && $row->requester_nik === $user->nik) {
                    $recallBtn = '<button type="button" class="btn btn-danger btn-sm action-btn-hover btn-recall-requisition" data-id="' . $row->id . '" data-tooltip="Recall"><i class="ph-bold ph-arrow-counter-clockwise"></i></button>';
                }

                // Tombol Duplicate hanya jika Recalled dan milik user login
                if ($row->status === 'Recalled' && $row->requester_nik === $user->nik) {
                    $duplicateBtn = '<button type="button" class="btn btn-warning btn-sm action-btn-hover btn-duplicate-requisition" data-id="' . $row->id . '" data-tooltip="Duplicate"><i class="ph-bold ph-copy"></i></button>';
                }

                return '<div class="action-btn-group gap-1 d-flex">' . $viewBtn . $recallBtn . $duplicateBtn . '</div>';
            })
            ->rawColumns(['no_srs', 'requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }
    
    //======================================================================
    // CRUD OPERATIONS
    //======================================================================

    public function store(StoreFreeGoodsRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user = User::with('atasan', 'department')->find(Auth::id());
            $userAccount = $user->department->code ?? null;

            // Logika Sub Category otomatis berdasarkan Dept
            if ($userAccount === '5300') {
                $pathSubCategory = 'SNM_PATH';
                $subCategoryLabel = 'SnM Request';
            } else {
                $pathSubCategory = 'NON_SNM_PATH';
                $subCategoryLabel = 'General Request';
            }

            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => $this->generateFgNumber(),
                'account' => $validated['account'],
                'cost_center' => $validated['cost_center'] ?? null,
                'request_date' => $validated['request_date'],
                'category' => 'FREE GOODS',
                'sub_category' => $subCategoryLabel,
                'objectives' => $validated['objectives'],
                'estimated_potential' => $validated['estimated_potential'],
                'status' => 'Pending',
                'route_to' => 'N/A',
            ]);

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'item_master_id' => $itemMasterId,
                    'material_type' => $subCategoryLabel, // Bisa disesuaikan
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued' => 0, // Default 0 saat create
                ]);
            }

            Log::info("Memulai proses approval untuk Free Goods Requisition #{$requisition->id}. Path: {$pathSubCategory}");

            $this->generateApprovalLogs(
                $user,
                $requisition->id,
                'FREE GOODS',
                $pathSubCategory // Gunakan sub_category khusus untuk path
            );

            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    $requisition->update(['route_to' => $firstApprover->name]);
                    sendFreeGoods::dispatch($requisition, $firstApprover, $firstLog->token, ['mail_type' => 'approval']);
                    
                    // Notifikasi Sistem
                    $notificationData = [
                        'requisition_id' => $requisition->id,
                        'srs_number'     => $requisition->no_srs,
                        'message'        => "FG Request #{$requisition->no_srs} dari {$user->name} menunggu approval Anda.",
                        // [FIX] Menggunakan nama route yang benar sesuai web.php
                        'url'            => route('freegoods-form.approval'), 
                    ];
                    $firstApprover->notify(new RequisitionNotification($notificationData, $user));

                } else {
                    $requisition->update(['status' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada alur approval Free Goods yang cocok. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();
            $nextFgNumber = $this->generateFgNumber();

            // Activity Log
            activity()
                ->causedBy($user)
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event('create')
                ->log("Membuat Free Goods Requisition baru #{$requisition->no_srs}");

            return response()->json([
                'success' => true,
                'message' => 'Free Goods Requisition berhasil dibuat dan permintaan persetujuan telah dikirim.',
                'next_fg_number' => $nextFgNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat Free Goods requisition: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function update(UpdateFreeGoodsRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $validated = $request->validated();

        $subCategoryLabel = $requisition->sub_category ?? 'General Request';

        DB::beginTransaction();
        try {

            $requisition->update($validated);
            $requisition->requisitionItems()->delete();

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id'    => $requisition->id,
                    'item_master_id'    => $itemMasterId,
                    'material_type'     => $subCategoryLabel,
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued'   => $itemData['quantity_issued'] ?? 0,
                ]);
            }

            $message = 'Free Goods Requisition berhasil diubah.';

            activity()
                ->causedBy(Auth::user())
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event('update')
                ->log("Mengupdate Free Goods Requisition #{$requisition->no_srs}");

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah Free Goods requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
        }
    }

    public function edit($id)
    {
        $requisition = Requisition::with([
            'requisitionItems.itemMaster',
        ])->findOrFail($id);

        $responseData = $requisition->toArray();

        $selectedMasterIds = $requisition->requisitionItems->pluck('item_master_id')->unique()->values()->all();

        $productOptions = ItemMaster::select('id', 'item_master_code', 'item_master_name')
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'text' => "[{$item->item_master_code}] {$item->item_master_name}"])
            ->toArray();

        $responseData['selected_master_ids'] = $selectedMasterIds;
        $responseData['product_options'] = $productOptions;

        return response()->json($responseData);
    }

    public function show($id)
    {
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email,avatar',
            'requisitionItems:requisition_id,item_master_id,quantity_required,quantity_issued',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'approvalLogs' => function ($query) {
                $query->orderBy('level', 'asc');
            },
            'approvalLogs.approver:nik,name,avatar',
            'trackings' => fn($q) => $q->orderBy('last_updated', 'asc'),
        ])->findOrFail($id);
    
        $history = [];
    
        // 1. Created
        $history[] = [
            'actor' => $requisition->requester->name ?? 'System',
            'avatar' => $requisition->requester->avatar ? asset($requisition->requester->avatar) : null,
            'action' => 'Created',
            'notes' => 'Requisition has been submitted.',
            'timestamp' => $requisition->created_at->toDateTimeString(),
        ];
    
        // 2. Approvals
        foreach ($requisition->approvalLogs as $log) {
            if ($log->status !== 'Pending') {
                $action_text = $log->status;
                if ($log->status === 'Approved' && !in_array($log->notes, ['Approved without Review'])) {
                    $action_text = 'Approved with Review';
                }
    
                $history[] = [
                    'actor' => $log->approver->name ?? 'Unknown Approver',
                    'avatar' => $log->approver->avatar ? asset($log->approver->avatar) : null,
                    'action' => $action_text,
                    'notes' => $log->notes,
                    'timestamp' => $log->responded_at ? $log->responded_at->toDateTimeString() : $log->updated_at->toDateTimeString(),
                ];
            }
        }
    
        // 3. Tracking (Warehouse)
        foreach ($requisition->trackings as $tracking) {
            if ($tracking->last_updated) {
                $history[] = [
                    'actor' => $tracking->current_position,
                    'avatar' => null,
                    'action' => 'Completed Step',
                    'notes' => $tracking->notes,
                    'timestamp' => $tracking->last_updated->toDateTimeString(),
                ];
            }
        }

        // 4. Recall
        if ($requisition->status === 'Recalled') {
            $history[] = [
                'actor' => $requisition->requester->name ?? 'Requester',
                'avatar' => $requisition->requester->avatar ? asset($requisition->requester->avatar) : null,
                'action' => 'Recalled',
                'notes' => 'Requisition was recalled by requester.',
                'timestamp' => $requisition->updated_at->toDateTimeString(),
            ];
        }
    
        usort($history, function ($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });
    
        $responseData = $requisition->toArray();
        $responseData['history'] = $history;
    
        return response()->json($responseData);
    }

    public function recallRequisition(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);
    
        DB::beginTransaction();
        try {
            $requisition = Requisition::with('requester')->findOrFail($id);
            $user = Auth::user();
    
            if ($requisition->status !== 'Pending') {
                return response()->json(['success' => false, 'message' => 'Requisition can no longer be recalled.'], 403);
            }
    
            $firstLog = ApprovalLog::where('requisition_id', $id)->orderBy('level', 'asc')->first();
            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    Log::info("Mengirim notifikasi recall untuk FG #{$id} ke {$firstApprover->name}");
    
                    dispatch(new sendFreeGoods($requisition, $firstApprover, null, [
                        'mail_type' => 'recalled_notification',
                        'notes'     => $request->input('notes') 
                    ]));

                    // Notifikasi Sistem
                    $firstApprover->notify(new RequisitionNotification([
                        'requisition_id' => $requisition->id,
                        'srs_number'     => $requisition->no_srs,
                        'message'        => "FG Request #{$requisition->no_srs} telah di-RECALL oleh {$user->name}.",
                        // [FIX] Nama Route yang benar
                        'url'            => route('freegoods-form.approval'),
                    ], $user));
                }
            }
    
            $requisition->update(['status' => 'Recalled', 'route_to' => 'Recalled by Requester']);
            
            // Tandai ApprovalLog agar tidak muncul di dashboard approver
            ApprovalLog::where('requisition_id', $id)->update(['status' => 'Recalled', 'token' => null]);
            
            activity()
                ->performedOn($requisition)
                ->causedBy($user)
                ->useLog('freegoods')
                ->event('recall')
                ->withProperties(['reason' => $request->input('notes')])
                ->log("Recalled Requisition #{$requisition->no_srs}");
    
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Requisition has been successfully recalled.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to recall Free Goods Requisition #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while recalling the requisition.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $requisition = Requisition::findOrFail($id);
            $requisition->delete();

            return response()->json(['success' => true, 'message' => 'Free Goods Requisition was successfully deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus requisition.'], 500);
        }
    }

    //======================================================================
    // APPROVAL PAGE (Internal Dashboard)
    //======================================================================

    public function approvalPage()
    {
        return view('page.freegoods.approval.index');
    }

    public function getApprovalData(Request $request)
    {
        $user = Auth::user();

        $query = ApprovalLog::with([
            'requisition.requester:nik,name,avatar',
            'requisition:id,no_srs,sub_category,status,request_date,requester_nik',
            'approver:nik,name,avatar'
        ])
        ->join('requisitions', 'approval_logs.requisition_id', '=', 'requisitions.id')
        ->where('requisitions.category', 'FREE GOODS');

        if ($user->hasRole('super-admin')) {
            // Super admin melihat semua
        } else {
            $query->where('approval_logs.approver_nik', $user->nik)
                  ->where('approval_logs.status', 'Pending')
                  ->whereIn('requisitions.status', ['Pending', 'In Progress', 'Approved'])
                  ->where(function ($q) {
                        // Tampilkan hanya jika level 1, ATAU level sebelumnya sudah Approved
                        $q->where('approval_logs.level', 1)
                        ->orWhereExists(function ($subQuery) {
                            $subQuery->select(DB::raw(1))
                                    ->from('approval_logs as prev_log')
                                    ->whereColumn('prev_log.requisition_id', 'approval_logs.requisition_id')
                                    ->whereColumn('prev_log.level', DB::raw('approval_logs.level - 1'))
                                    ->where('prev_log.status', 'Approved');
                        });
                  });
        }

        $query->select('approval_logs.*')->orderBy('approval_logs.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_srs', fn($row) => '<span class="badge-custom badge-fg-no"># ' . e($row->requisition->no_srs ?? 'N/A') . '</span>')
            ->addColumn('requester', function ($row) {
                $avatar = $row->requisition->requester->avatar ? asset($row->requisition->requester->avatar) : asset('assets/images/logo/sinarmeadow.png');
                return '<div class="status-badge-lg bg-dark d-flex align-items-center"><img src="' . $avatar . '" class="img-fluid rounded-circle me-1" style="width:20px;"> ' . e($row->requisition->requester->name ?? 'Unknown') . '</div>';
            })
            ->addColumn('request_date', fn($row) => Carbon::parse($row->requisition->request_date)->format('d M Y'))
            ->addColumn('sub_category', fn($row) => '<span class="badge-custom badge-category">' . e($row->requisition->sub_category ?? '-') . '</span>')
            ->addColumn('level', fn($row) => '<span class="status-badge-lg bg-primary">Lvl ' . $row->level . '</span>')
            ->editColumn('status', function ($row) {
                $status = $row->requisition->status ?? 'N/A';
                return '<span class="badge-custom badge-status-pending">' . e($status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $token = $row->token;
                $srs = $row->requisition->no_srs;
                $id = $row->requisition->id;

                $approveBtn = '<button class="btn btn-success btn-sm action-btn" data-token="'.$token.'" data-srs="'.$srs.'" data-tooltip="Quick Approve"><i class="ph-bold ph-check-circle"></i></button>';
                $reviewBtn = '<button class="btn btn-info btn-sm action-btn-modal" data-id="'.$id.'" data-token="'.$token.'" data-srs="'.$srs.'" data-action="review" data-tooltip="Review & Approve"><i class="ph-bold ph-pencil-simple"></i></button>';
                $rejectBtn = '<button class="btn btn-danger btn-sm action-btn-modal" data-id="'.$id.'" data-token="'.$token.'" data-srs="'.$srs.'" data-action="reject" data-tooltip="Reject"><i class="ph-bold ph-x-circle"></i></button>';

                return '<div class="action-btn-group gap-1 d-flex justify-content-center">' . $approveBtn . $reviewBtn . $rejectBtn . '</div>';
            })
            ->rawColumns(['no_srs', 'requester', 'sub_category', 'level', 'status', 'action'])
            ->make(true);
    }

    //======================================================================
    // REPORTING & LOGGING
    //======================================================================
    
    public function reports()
    {
        return view('page.freegoods.report.index');
    }

    public function getReportData(Request $request)
    {
        $query = Requisition::with(['requester:nik,name', 'customer:id,name'])
            ->where('category', 'FREE GOODS');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('request_date', [$startDate, $endDate]);
        }

        if (!Auth::user()->hasRole('super-admin')) {
            $query->where('requester_nik', Auth::user()->nik);
        }

        return DataTables::of($query)
            ->addColumn('checkbox', function ($requisition) {
                return '<input type="checkbox" class="form-check-input requisition-checkbox" value="' . $requisition->id . '">';
            })
            ->editColumn('no_srs', function ($req) {
                return '<span class="badge-custom badge-fg-no"># ' . e($req->no_srs) . '</span>';
            })
            ->addColumn('requester_info', function ($requisition) {
                $name = e($requisition->requester->name ?? 'N/A');
                return '<div class="requester-badge"><i class="ph-bold ph-user-circle"></i><span>' . $name . '</span></div>';
            })
            ->addColumn('customer_name', fn ($req) => e($req->customer->name ?? 'N/A'))
            ->editColumn('request_date', fn($req) => Carbon::parse($req->request_date)->format('d M Y'))
            ->editColumn('sub_category', function ($requisition) {
                return '<span class="badge-custom badge-category">' . e($requisition->sub_category) . '</span>';
            })
            ->editColumn('status', function ($requisition) {
                $status = $requisition->status;
                $badgeClass = 'bg-secondary';
                if (in_array($status, ['Approved', 'Completed'])) {
                    $badgeClass = 'bg-success';
                } elseif (in_array($status, ['Rejected', 'Recalled'])) {
                    $badgeClass = 'bg-danger';
                } elseif (in_array($status, ['Submitted', 'Pending', 'In Progress'])) {
                    $badgeClass = 'badge-status-pending';
                }
                return '<span class="badge-custom ' . $badgeClass . '">' . e($status) . '</span>';
            })
            ->rawColumns(['checkbox', 'no_srs', 'requester_info', 'sub_category', 'status'])
            ->make(true);
    }

    public function printBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $requisitionIds = $request->input('ids');

        $requisitions = Requisition::with([
            'customer',
            'requester.department',
            'requisitionItems.itemMaster',
            'approvalLogs.approver.roles' // Load roles for print
        ])->whereIn('id', $requisitionIds)
          ->orderBy('no_srs', 'asc')
          ->get();

        if ($requisitions->isEmpty()) {
            return back()->with('error', 'No requisitions selected or found for printing.');
        }

        $pdf = Pdf::loadView('page.freegoods.report.print', compact('requisitions'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('FreeGoods-Batch-Print.pdf');
    }

    public function log()
    {
        return view('page.freegoods.log.index');
    }

    public function getLogData()
    {
        $query = Activity::with(['causer', 'subject'])
            ->where('log_name', 'freegoods')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('log_name', fn($log) => '<span class="badge-custom bg-dark"><i class="ph-bold ph-cube me-1"></i>Free Goods</span>')
            ->editColumn('event', function ($log) {
                $event = e(ucfirst($log->event));
                $badgeClass = 'bg-secondary';
                if ($log->event === 'create') $badgeClass = 'bg-primary';
                if ($log->event === 'update' || $log->event === 'approve') $badgeClass = 'bg-warning';
                if ($log->event === 'delete' || $log->event === 'reject') $badgeClass = 'bg-danger';
                if ($log->event === 'tracking') $badgeClass = 'bg-info';
                return '<span class="status-badge-lg ' . $badgeClass . '">' . $event . '</span>';
            })
            ->addColumn('subject_info', function ($log) {
                if ($log->subject && $log->subject->no_srs) {
                    return '<span class="badge-custom badge-fg-no"># ' . e($log->subject->no_srs) . '</span>';
                }
                return '<span class="badge bg-light text-dark">N/A</span>';
            })
            ->addColumn('subject_id', fn($log) => $log->subject_id)
            ->addColumn('causer_info', function ($log) {
                if ($log->causer && $log->causer->name) {
                    return '<div class="requester-badge"><i class="ph-bold ph-user-circle"></i><span>' . e($log->causer->name) . '</span></div>';
                }
                return '<span class="badge bg-light text-dark">System</span>';
            })
            ->editColumn('created_at', fn($log) => Carbon::parse($log->created_at)->format('d M Y, H:i:s'))
            ->rawColumns(['log_name', 'event', 'subject_info', 'causer_info'])
            ->make(true);
    }

    //======================================================================
    // APPROVAL RESPONSE HANDLING (Email Links)
    //======================================================================

    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        $originalAction = $action;
        $validActions = ['approve', 'review', 'reject', 'submit', 'update_qty'];

        if (!in_array($action, $validActions)) {
            return view('page.freegoods.links.invalid', ['message' => 'Invalid action.']);
        }

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        $tracking = !$approvalLog ? Tracking::where('token', $token)->whereNull('last_updated')->first() : null;

        if (!$approvalLog && !$tracking) {
            return view('page.freegoods.links.invalid', ['message' => 'This request is invalid or has been processed.']);
        }

        // Quick Approve tanpa form
        if ($action === 'approve' && !$tracking) {
            $request->merge(['token' => $token, 'action' => 'approve', 'notes' => 'Approved via quick action link.']);
            return $this->processApproval($request);
        }

        $requisition = $approvalLog ? $approvalLog->requisition : $tracking->requisition;
        $requisition->load('requester.department', 'customer', 'requisitionItems.itemMaster', 'approvalLogs.approver');

        $isWarehouseProcess = (bool)$tracking;
        $pageTitle = $isWarehouseProcess ? ($tracking->current_position ?? 'Warehouse Process') : 'Approval Action';

        if ($action === 'reject') {
            $action = 'review'; // Agar form muncul
        }

        $viewData = [
            'token' => $token,
            'action' => $action,
            'requisition' => $requisition,
            'pageTitle' => $pageTitle,
            'isWarehouseProcess' => $isWarehouseProcess,
            'originalAction' => $originalAction,
        ];

        return view('page.freegoods.links.response-form', $viewData);
    }

    public function processApproval(Request $request)
    {
        if ($request->input('action') === 'approve' &&
            $request->input('notes') === 'Approved via quick action link.')
        {
            $validated = $request->all();
        } else {
            $validated = $request->validate([
                'token' => 'required|string',
                'action' => 'required|string|in:approve,review,reject,submit,update_qty',
                'notes' => 'nullable|string|max:500|required_if:action,review,reject,update_qty',
                'items' => 'nullable|array',
                'items.*' => 'nullable|integer|min:0',
            ]);
        }

        $token = $validated['token'];
        $action = $validated['action'];
        $notes = $validated['notes'] ?? null;

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if ($approvalLog) {
            return $this->processApprovalStep($approvalLog, $action, $notes);
        }

        $tracking = Tracking::where('token', $token)->whereNull('last_updated')->first();

        if ($tracking) {
            return $this->processWarehouseStep($tracking, $action, $notes, $validated['items'] ?? []);
        }

        return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request')->withMessage('This approval request is invalid or has already been processed.');
    }

    private function processApprovalStep(ApprovalLog $approvalLog, string $action, ?string $notes)
    {
        DB::beginTransaction();
        try {
            $requisition = $approvalLog->requisition->load('customer', 'requester');
            $approverName = $approvalLog->approver->name ?? 'Approver';
            $finalNotes = $notes;

            $logStatus = ($action === 'reject') ? 'Rejected' : 'Approved';
            $cardClass = ($logStatus === 'Rejected') ? 'reject' : 'success';

            if ($logStatus === 'Rejected') {
                $finalNotes = $notes ?: 'Rejected without reason';
            } elseif (empty($notes)) {
                $finalNotes = 'Approved by ' . $approverName;
            }

            $approvalLog->update([
                'status'       => $logStatus,
                'notes'        => $finalNotes,
                'responded_at' => now(),
                'token'        => null,
            ]);

            $title = 'Action Submitted';
            $actionText = ucfirst($logStatus);
            $newStatus = 'In Progress';

            if ($logStatus === 'Rejected') {
                $requisition->update(['status' => 'Rejected', 'route_to' => 'Finished (Rejected)']);

                if ($requisition->requester?->email) {
                    Mail::to($requisition->requester->email)->send(new MailRejectFreeGoods($requisition, $approverName, $finalNotes));
                }
                
                // Notifikasi Sistem Reject
                if($requisition->requester) {
                    $requisition->requester->notify(new RequisitionNotification([
                        'requisition_id' => $requisition->id,
                        'srs_number'     => $requisition->no_srs,
                        'message'        => "FG Request #{$requisition->no_srs} telah di-REJECT oleh {$approverName}.",
                        // [FIX] Nama Route yang benar
                        'url'            => route('freegoods-form.index'), 
                    ], $approvalLog->approver));
                }

                $title = 'Requisition Rejected';
                $newStatus = 'Rejected';

            } else { // Approved
                if (!str_starts_with($finalNotes, 'Approved by')) {
                    $title = 'Approved with Review';
                    $actionText = 'Approved with Review';
                }

                $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                                ->where('level', '>', $approvalLog->level)
                                                ->orderBy('level', 'asc')->first();

                if ($nextApprovalLog) {
                    $nextApprover = User::where('nik', $nextApprovalLog->approver_nik)->first();
                    if ($nextApprover) {
                        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprover->name]);
                        dispatch(new sendFreeGoods($requisition, $nextApprover, $nextApprovalLog->token, ['mail_type' => 'approval']));
                        
                        // Notifikasi Sistem
                        $nextApprover->notify(new RequisitionNotification([
                            'requisition_id' => $requisition->id,
                            'srs_number'     => $requisition->no_srs,
                            'message'        => "FG Request #{$requisition->no_srs} menunggu approval Anda.",
                            // [FIX] Nama Route yang benar
                            'url'            => route('freegoods-form.approval'), 
                        ], $requisition->requester));

                        $newStatus = "Waiting for {$nextApprover->name}";
                    } else {
                        Log::error("Approver berikutnya dengan NIK {$nextApprovalLog->approver_nik} tidak ditemukan.");
                        $requisition->update(['status' => 'Error', 'route_to' => 'Error: Approver Not Found']);
                        $newStatus = 'Error';
                        $cardClass = 'reject';
                    }
                } else {
                    $requisition->update(['status' => 'Approved']);
                    $newStatus = $this->startPostApprovalProcess($requisition);
                }
            }

            activity()
                ->causedBy($approvalLog->approver)
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event($logStatus === 'Rejected' ? 'reject' : 'approve')
                ->log("{$logStatus} Free Goods Requisition #{$requisition->no_srs}");

            DB::commit();

            return redirect()->route('fg.approval.success')
                ->with('card_class', $cardClass)
                ->with('title', $title)
                ->with('message', 'Your response has been successfully recorded.')
                ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                ->with('action_text', $actionText)
                ->with('approver_name', $approverName)
                ->with('new_status', $newStatus);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal proses approval Free Goods #{$approvalLog->id}: " . $e->getMessage());
            return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred during approval process.');
        }
    }

    private function processWarehouseStep(Tracking $tracking, string $action, ?string $notes, array $items)
    {
        DB::beginTransaction();
        try {
            $requisition = $tracking->requisition;
            Log::info("Processing warehouse step for Free Goods Requisition #{$requisition->id}. Current position: {$tracking->current_position}.");

            if (!empty($items)) {
                foreach ($items as $itemId => $qty) {
                     RequisitionItem::where('id', $itemId)
                        ->where('requisition_id', $requisition->id)
                        ->update(['quantity_issued' => $qty]);
                }
            }

            $defaultNote = "Proses {$tracking->current_position} berhasil disubmit tanpa notes.";
            if ($action === 'update_qty' && empty($notes)) {
                 $defaultNote = "Quantity updated via warehouse process.";
            }

            $tracking->update([
                'token' => null,
                'last_updated' => now(),
                'notes' => $notes ?: $defaultNote
            ]);

            // Mencatat siapa user yang melakukan aksi (fallback ke sistem jika tidak login)
            $causer = Auth::user() ?? $this->findUserForStep($tracking->current_position);
            activity()
                ->causedBy($causer)
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event('tracking')
                ->log("Completed warehouse step: {$tracking->current_position}");

            $newStatus = $this->advanceWarehouseStep($requisition);

            DB::commit();

            $requisition->load('customer');

            return redirect()->route('fg.approval.success')
                ->with('card_class', 'success')->with('title', 'Warehouse Step Completed')
                ->with('message', 'Warehouse process step has been recorded.')
                ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                ->with('action_text', $action === 'update_qty' ? 'Qty Updated' : 'Processed')
                ->with('approver_name', $tracking->current_position)
                ->with('new_status', $newStatus);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal melanjutkan proses warehouse Free Goods: " . $e->getMessage());
            return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred. Please check the system logs.');
        }
    }

    private function startPostApprovalProcess(Requisition $requisition)
    {
        Log::info("Approval path selesai untuk Free Goods Requisition #{$requisition->id}. Memulai proses warehouse.");

        $this->generateTrackingPath($requisition->id, 'FREE GOODS', $requisition->sub_category);

        $firstTracking = Tracking::where('requisition_id', $requisition->id)
                                 ->whereNull('last_updated')
                                 ->orderBy('id', 'asc')
                                 ->first();

        if ($firstTracking) {
            $stepName = $firstTracking->current_position;
            $requisition->update(['status' => 'Processing', 'route_to' => $stepName]);

            $user = User::where('nik', $firstTracking->approver_nik)->first();
            if ($user) {
                $baseUrl = route('fg.approval.response', ['token' => $firstTracking->token]);
                dispatch(new sendFreeGoods($requisition, $user, $firstTracking->token, [
                    'mail_type'    => 'warehouse_process',
                    'process_step' => $stepName,
                    'submit_url'     => $baseUrl . '?action=submit',
                    'review_url'     => $baseUrl . '?action=review',
                    'update_qty_url' => $baseUrl . '?action=update_qty',
                ]));
            }
            return $stepName;
        } else {
            $stepName = 'Processing';
            $requisition->update(['status' => 'Processing', 'route_to' => $stepName]);
            return $stepName;
        }
    }

    private function advanceWarehouseStep(Requisition $requisition)
    {
        $nextTracking = Tracking::where('requisition_id', $requisition->id)
                                ->whereNull('last_updated')
                                ->orderBy('id', 'asc')
                                ->first();

        if ($nextTracking) {
            $requisition->update(['route_to' => $nextTracking->current_position]);

            $user = User::where('nik', $nextTracking->approver_nik)->first();
            if ($user) {
                $baseUrl = route('fg.approval.response', ['token' => $nextTracking->token]);
                dispatch(new sendFreeGoods($requisition, $user, $nextTracking->token, [
                    'mail_type'    => 'warehouse_process',
                    'process_step' => $nextTracking->current_position,
                    'submit_url'     => $baseUrl . '?action=submit',
                    'review_url'     => $baseUrl . '?action=review',
                    'update_qty_url' => $baseUrl . '?action=update_qty',
                ]));
            }
            
            return "Processing (" . $nextTracking->current_position . ")";
        } else {
            return $this->notifyRequesterAsCompleted($requisition);
        }
    }

    private function notifyRequesterAsCompleted(Requisition $requisition)
    {
        $statusText = 'Completed';
        $requisition->update(['status' => $statusText, 'route_to' => 'Finished']);
        
        if ($requisition->requester?->email) {
            dispatch(new sendFreeGoods($requisition, $requisition->requester, null, [
                'mail_type' => 'completed_notification'
            ]));

            $requisition->requester->notify(new RequisitionNotification([
                'requisition_id' => $requisition->id,
                'srs_number'     => $requisition->no_srs,
                'message'        => "FG Request #{$requisition->no_srs} telah SELESAI diproses.",
                // [FIX] Nama Route yang benar
                'url'            => route('freegoods-form.index'), 
            ], $requisition->requester));
        }
        Log::info("Free Goods Requisition #{$requisition->id} selesai. Notifikasi dikirim ke requester.");
        return $statusText;
    }

    public function showSuccessPage()
    {
        if (!session('title')) {
            return redirect('/');
        }
        return view('page.freegoods.links.response-success');
    }
}