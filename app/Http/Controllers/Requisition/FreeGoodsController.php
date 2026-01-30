<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\Requisition\Tracking;
use App\Models\User;
use App\Models\Requisition\ApprovalLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str; 
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ApprovalTrait;
use App\Traits\traitTracking;
use App\Notifications\RequisitionNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\sendFreeGoods;
use App\Mail\MailRejectFreeGoods;

class FreeGoodsController extends Controller
{
    use ApprovalTrait;
    use traitTracking;

    //======================================================================
    // PRIVATE HELPERS
    //======================================================================

    private function generateFgNumber()
    {
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

    /**
     * Mencari User untuk step Warehouse/Outward dengan cerdas.
     */
    private function findUserForStep(string $stepName)
    {
        try {
            // 1. Cari berdasarkan NAMA (Exact Match)
            $user = User::where('name', $stepName)->first();
            if ($user) return $user;

            // 2. Cari berdasarkan Job Title
            $user = User::where('job_title', $stepName)->first();
            if ($user) return $user;

            // 3. Logic Khusus: Cari yang mengandung kata "Outward" atau "Warehouse"
            // Ini penting untuk menemukan Supervisor WH jika nama tidak exact match
            if (str_contains(strtolower($stepName), 'outward') || str_contains(strtolower($stepName), 'warehouse') || str_contains(strtoupper($stepName), 'WH')) {
                $user = User::where(function($q) {
                                $q->where('name', 'LIKE', '%Outward%')
                                  ->orWhere('job_title', 'LIKE', '%Outward%')
                                  ->orWhere('name', 'LIKE', '%Warehouse%')
                                  ->orWhere('job_title', 'LIKE', '%Warehouse%');
                            })
                            ->first();
                if ($user) return $user;
            }

            Log::warning("User untuk step '{$stepName}' tidak ditemukan. Menggunakan Fallback User (Auth/Admin).");

            // 5. FALLBACK TERAKHIR (Agar sistem tidak error, ambil admin atau user aktif)
            return Auth::user() ?? User::first();

        } catch (\Exception $e) {
            Log::error("Error finding user for step '{$stepName}': " . $e->getMessage());
            return Auth::user() ?? User::first();
        }
    }

    private function isHcdDepartment($user)
    {
        $deptName = strtoupper($user->department->name ?? '');
        return str_contains($deptName, 'HUMAN CAPITAL') || 
               str_contains($deptName, 'HCD') || 
               str_contains($deptName, 'HRD');
    }

    private function isSalesDepartment($user)
    {
        $deptCode = $user->department->code ?? '';
        $deptName = strtoupper($user->department->name ?? '');
        return $deptCode === '5300' || 
               str_contains($deptName, 'SALES') || 
               str_contains($deptName, 'MARKETING');
    }

    //======================================================================
    // PUBLIC ENDPOINTS
    //======================================================================

    public function getAllItemMasters()
    {
        $masters = ItemMaster::select('id', 'item_master_code', 'item_master_name', 'unit')->get();
        return response()->json($masters);
    }

    public function index()
    {
        $user = Auth::user();
        $userDepartmentName = $user->department?->name ?? null;
        $isHcd = $this->isHcdDepartment($user);

        return view('page.freegoods.index', compact(
            'userDepartmentName', 'isHcd'
        ));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        
        $query = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->where('requisitions.category', 'FREE GOODS')
            ->select(
                'requisitions.id', 
                'requisitions.no_srs', 
                'requisitions.requester_nik', 
                'requisitions.request_date', 
                'requisitions.created_at', 
                'requisitions.cost_center', 
                'requisitions.sub_category', 
                'requisitions.route_to', 
                'requisitions.status', 
                'requisitions.recipient_name', 
                'users.name as requester_name', 
                'users.avatar'
            );

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('requisitions.status', $request->status);
        }

        if (!$user->hasRole('super-admin')) {
             $query->where('requisitions.requester_nik', $user->nik);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('requester_info', function ($req) {
                $avatarUrl = $req->avatar ? asset($req->avatar) : asset('assets/images/logo/sinarmeadow.png');
                $name = e($req->requester_name);
                return '
                <div class="badge-requester">
                    <img src="'.$avatarUrl.'" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                    <span>'.$name.'</span>
                </div>';
            })
            ->editColumn('request_date', fn($req) => Carbon::parse($req->created_at)->format('d M Y'))
            ->addColumn('recipient_name', fn($req) => e($req->recipient_name))
            ->editColumn('sub_category', fn($req) => '<span class="badge rounded-pill bg-info text-white text-uppercase" style="font-size: 0.75rem; padding: 6px 12px;">' . strtoupper(e($req->sub_category)) . '</span>')
            ->editColumn('route_to', fn($req) => '<span class="badge-custom badge-route-to"><i class="ph-bold ph-user-switch me-1"></i>' . e($req->route_to) . '</span>')
            ->editColumn('status', function ($req) {
                $status = $req->status;
                $badgeClass = 'status-default';
                if (in_array($status, ['Submitted', 'Pending'])) {
                    $badgeClass = 'status-pending';
                } elseif (in_array($status, ['In Progress', 'Processing'])) {
                    $badgeClass = 'status-processing';
                } elseif (in_array($status, ['Approved', 'Completed'])) {
                    $badgeClass = 'status-completed';
                } elseif (in_array($status, ['Rejected', 'Recalled', 'Cancelled'])) {
                    $badgeClass = 'status-rejected';
                }
                return '<span class="badge rounded-pill '.$badgeClass.' text-uppercase shadow-sm" style="min-width: 90px; padding: 6px 0;">' . strtoupper(e($status)) . '</span>';
            })
            ->addColumn('action', function ($row) use ($user) {
                $viewBtn = '<button type="button" class="btn btn-info btn-sm action-btn-hover btn-view-requisition" data-id="' . $row->id . '" data-tooltip="View Details"><i class="ph-bold ph-eye"></i></button>';
                $recallBtn = '';
                $duplicateBtn = '';
                $deleteBtn = ''; 

                if ($row->status === 'Pending' && $row->requester_nik === $user->nik) {
                    $recallBtn = '<button type="button" class="btn btn-warning btn-sm action-btn-hover btn-recall-requisition" data-id="' . $row->id . '" data-tooltip="Recall"><i class="ph-bold ph-arrow-counter-clockwise"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-danger btn-sm action-btn-hover btn-delete-requisition" data-id="' . $row->id . '" data-tooltip="Delete"><i class="ph-bold ph-trash"></i></button>';
                }

                if ($row->status === 'Recalled' && $row->requester_nik === $user->nik) {
                    $duplicateBtn = '<button type="button" class="btn btn-warning btn-sm action-btn-hover btn-duplicate-requisition" data-id="' . $row->id . '" data-tooltip="Duplicate"><i class="ph-bold ph-copy"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-danger btn-sm action-btn-hover btn-delete-requisition" data-id="' . $row->id . '" data-tooltip="Delete"><i class="ph-bold ph-trash"></i></button>';
                }

                return '<div class="action-btn-group gap-1 d-flex justify-content-center">' . $viewBtn . $recallBtn . $duplicateBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    //======================================================================
    // CRUD OPERATIONS
    //======================================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:30', 
            'recipient_address' => 'nullable|string', 
            'request_date' => 'required|date',
            'objectives' => 'required|string',
            'cost_center' => 'nullable|string',
            'items' => 'required|array',
            'items.*.quantity_required' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $user = User::with('atasan', 'department')->find(Auth::id());
            
            $finalAccount = '5300';
            if ($this->isHcdDepartment($user)) {
                $finalCostCenter = '313';
            } else {
                $finalCostCenter = $validated['cost_center'] ?? null;
            }

            if ($this->isSalesDepartment($user)) {
                $pathSubCategory = 'SNM_PATH';
                $subCategoryLabel = 'SnM Request';
            } else {
                $pathSubCategory = 'NON_SNM_PATH';
                $subCategoryLabel = 'General Request';
            }

            $generatedNoSrs = $this->generateFgNumber();

            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'recipient_name' => $validated['recipient_name'], 
                'recipient_address' => $validated['recipient_address'], 
                'customer_id' => null, 
                'no_srs' => $generatedNoSrs, 
                'account' => $finalAccount,
                'cost_center' => $finalCostCenter,
                'request_date' => $validated['request_date'],
                'category' => 'FREE GOODS',
                'sub_category' => $subCategoryLabel,
                'objectives' => $validated['objectives'],
                'status' => 'Pending',
                'route_to' => 'N/A',
            ]);

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'item_master_id' => $itemMasterId,
                    'material_type' => $subCategoryLabel,
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued' => 0,
                ]);
            }

            Log::info("Free Goods Created: ID {$requisition->id}, Path: {$pathSubCategory}");

            $this->generateApprovalLogs(
                $user,
                $requisition->id,
                'FREE GOODS',
                $pathSubCategory
            );

            // Kirim Email ke Approver Pertama
            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    $requisition->update(['route_to' => $firstApprover->name]);
                    
                    sendFreeGoods::dispatch($requisition, $firstApprover, $firstLog->token, ['mail_type' => 'approval'])
                        ->delay(now()->addSeconds(3));
                    
                    $notificationData = [
                        'requisition_id' => $requisition->id,
                        'srs_number'     => $requisition->no_srs,
                        'message'        => "FG Request dari {$user->name} menunggu approval Anda.",
                        'url'            => route('freegoods-form.approval'), 
                    ];
                    $firstApprover->notify(new RequisitionNotification($notificationData, $user));

                } else {
                    $requisition->update(['status' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                }
            } else {
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
            }

            DB::commit();

            activity()
                ->causedBy($user)
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event('create')
                ->log("Membuat Free Goods Requisition baru");

            return response()->json([
                'success' => true,
                'message' => 'Free Goods Requisition berhasil dibuat dan permintaan persetujuan telah dikirim.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat Free Goods requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $requisition = Requisition::findOrFail($id);

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:30',
            'recipient_address' => 'nullable|string',
            'request_date' => 'required|date',
            'objectives' => 'required|string',
            'cost_center' => 'nullable|string',
            'items' => 'required|array',
            'items.*.quantity_required' => 'required|integer|min:1',
            'items.*.quantity_issued' => 'nullable|integer',
        ]);

        $subCategoryLabel = $requisition->sub_category ?? 'General Request';

        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            
            if ($this->isHcdDepartment($user)) {
                $validated['cost_center'] = '313';
            }

            $requisition->update([
                'recipient_name' => $validated['recipient_name'],
                'recipient_address' => $validated['recipient_address'],
                'request_date' => $validated['request_date'],
                'objectives' => $validated['objectives'],
                'cost_center' => $validated['cost_center'],
                'account' => '5300',
            ]);

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

            activity()
                ->causedBy(Auth::user())
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event('update')
                ->log("Mengupdate Free Goods Requisition");

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Free Goods Requisition berhasil diubah.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah Free Goods requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
        }
    }

    public function edit($id)
    {
        $requisition = Requisition::with(['requisitionItems.itemMaster'])->findOrFail($id);
        $responseData = $requisition->toArray();
        
        $selectedMasterIds = $requisition->requisitionItems->pluck('item_master_id')->unique()->values()->all();
        $productOptions = ItemMaster::select('id', 'item_master_code', 'item_master_name')->get()
            ->map(fn($item) => ['id' => $item->id, 'text' => "[{$item->item_master_code}] {$item->item_master_name}"])->toArray();

        $responseData['selected_master_ids'] = $selectedMasterIds;
        $responseData['product_options'] = $productOptions;

        return response()->json($responseData);
    }

    public function show($id)
    {
        $requisition = Requisition::with([
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
    
        $history[] = [
            'actor' => $requisition->requester->name ?? 'System',
            'avatar' => $requisition->requester->avatar ? asset($requisition->requester->avatar) : null,
            'action' => 'Created',
            'notes' => 'Requisition has been submitted.',
            'timestamp' => $requisition->created_at->toDateTimeString(),
        ];
    
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
        $request->validate(['notes' => 'required|string|max:500']);
    
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
                    dispatch(new sendFreeGoods($requisition, $firstApprover, null, [
                        'mail_type' => 'recalled_notification',
                        'notes'     => $request->input('notes') 
                    ]))->delay(now()->addSeconds(3));

                    $firstApprover->notify(new RequisitionNotification([
                        'requisition_id' => $requisition->id,
                        'srs_number'     => $requisition->no_srs,
                        'message'        => "FG Request dari {$user->name} telah di-RECALL.",
                        'url'            => route('freegoods-form.approval'),
                    ], $user));
                }
            }
    
            $requisition->update(['status' => 'Recalled', 'route_to' => 'Recalled by Requester']);
            ApprovalLog::where('requisition_id', $id)->update(['status' => 'Recalled', 'token' => null]);
            
            activity()
                ->performedOn($requisition)
                ->causedBy($user)
                ->useLog('freegoods')
                ->event('recall')
                ->withProperties(['reason' => $request->input('notes')])
                ->log("Recalled Requisition");
    
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
    // APPROVAL PAGE
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
                  ->where(function ($q) {
                        $q->where('approval_logs.status', 'Pending')
                          ->whereIn('requisitions.status', ['Pending', 'In Progress', 'Approved']);
                        $q->orWhere('approval_logs.status', 'Approved');
                  })
                  ->where(function ($q) {
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

        $query->select('approval_logs.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('requester', function ($row) {
                $avatar = $row->requisition->requester->avatar ? asset($row->requisition->requester->avatar) : asset('assets/images/logo/sinarmeadow.png');
                $name = e($row->requisition->requester->name ?? 'Unknown');
                return '
                <div class="badge-requester">
                    <img src="'.$avatar.'" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                    <span>'.$name.'</span>
                </div>';
            })
            ->addColumn('request_date', fn($row) => Carbon::parse($row->requisition->request_date)->format('d M Y'))
            ->addColumn('sub_category', fn($row) => '<span class="badge rounded-pill bg-info text-white text-uppercase" style="font-size: 0.75rem; padding: 6px 12px;">' . strtoupper(e($row->requisition->sub_category ?? '-')) . '</span>')
            ->addColumn('level', fn($row) => '<span class="badge badge-level"><i class="ph-fill ph-star me-1 text-warning"></i>LVL ' . $row->level . '</span>')
            ->editColumn('status', function ($row) {
                $status = $row->requisition->status ?? 'N/A';
                $badgeClass = 'status-default';
                if (in_array($status, ['Submitted', 'Pending'])) {
                    $badgeClass = 'status-pending';
                } elseif (in_array($status, ['In Progress', 'Processing'])) {
                    $badgeClass = 'status-processing';
                } elseif (in_array($status, ['Approved', 'Completed'])) {
                    $badgeClass = 'status-completed';
                } elseif (in_array($status, ['Rejected', 'Recalled', 'Cancelled'])) {
                    $badgeClass = 'status-rejected';
                }
                return '<span class="badge rounded-pill '.$badgeClass.' text-uppercase shadow-sm" style="min-width: 90px; padding: 6px 0;">' . strtoupper(e($status)) . '</span>';
            })
            ->editColumn('approver_nik', function ($row) {
                $nik = $row->approver_nik ?? '-';
                return '<span class="badge-approver"><i class="ph-bold ph-user-circle me-1"></i>' . e($nik) . '</span>';
            })
            ->addColumn('action', function ($row) {
                if ($row->status === 'Approved') {
                    $date = $row->responded_at ? Carbon::parse($row->responded_at)->format('d M Y, H:i') : 'N/A';
                    return '<div class="d-flex justify-content-center align-items-center text-success" 
                                 data-bs-toggle="tooltip" 
                                 data-bs-placement="top" 
                                 title="Approved on: ' . $date . '">
                                <i class="ph-fill ph-check-circle" style="font-size: 1.8rem;"></i>
                            </div>';
                }
                if ($row->status === 'Pending') {
                    $token = $row->token;
                    $srs = $row->requisition->no_srs;
                    $id = $row->requisition->id;

                    $approveBtn = '<button class="btn btn-success btn-sm action-btn" data-token="'.$token.'" data-srs="'.$srs.'" data-tooltip="Quick Approve"><i class="ph-bold ph-check-circle"></i></button>';
                    $reviewBtn = '<button class="btn btn-info btn-sm action-btn-modal" data-id="'.$id.'" data-token="'.$token.'" data-srs="'.$srs.'" data-action="review" data-tooltip="Review & Approve"><i class="ph-bold ph-pencil-simple"></i></button>';
                    $rejectBtn = '<button class="btn btn-danger btn-sm action-btn-modal" data-id="'.$id.'" data-token="'.$token.'" data-srs="'.$srs.'" data-action="reject" data-tooltip="Reject"><i class="ph-bold ph-x-circle"></i></button>';

                    return '<div class="action-btn-group gap-1 d-flex justify-content-center">' . $approveBtn . $reviewBtn . $rejectBtn . '</div>';
                }
                return '-';
            })
            ->rawColumns(['requester', 'sub_category', 'level', 'status', 'approver_nik', 'action'])
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
        $query = Requisition::with(['requester:nik,name'])
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
            ->addColumn('checkbox', fn($req) => '<input type="checkbox" class="form-check-input requisition-checkbox" value="' . $req->id . '">')
            ->editColumn('no_srs', fn($req) => '<span class="badge-custom badge-fg-no"># ' . e($req->no_srs) . '</span>')
            ->addColumn('requester_info', function ($requisition) {
                $name = e($requisition->requester->name ?? 'N/A');
                $avatar = $requisition->requester->avatar ?? null;
                $avatarUrl = $avatar ? asset($avatar) : asset('assets/images/logo/sinarmeadow.png');
                return '
                <div class="badge-requester">
                    <img src="'.$avatarUrl.'" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                    <span>'.$name.'</span>
                </div>';
            })
            ->addColumn('customer_name', fn ($req) => e($req->recipient_name ?? 'N/A'))
            ->editColumn('request_date', fn($req) => Carbon::parse($req->request_date)->format('d M Y'))
            ->editColumn('sub_category', fn($req) => '<span class="badge rounded-pill bg-info text-white text-uppercase" style="font-size: 0.75rem; padding: 6px 12px;">' . strtoupper(e($req->sub_category)) . '</span>')
            ->editColumn('status', function ($requisition) {
                $status = $requisition->status;
                $badgeClass = 'status-default';
                if (in_array($status, ['Submitted', 'Pending'])) {
                    $badgeClass = 'status-pending';
                } elseif (in_array($status, ['In Progress', 'Processing'])) {
                    $badgeClass = 'status-processing';
                } elseif (in_array($status, ['Approved', 'Completed'])) {
                    $badgeClass = 'status-completed';
                } elseif (in_array($status, ['Rejected', 'Recalled', 'Cancelled'])) {
                    $badgeClass = 'status-rejected';
                }
                return '<span class="badge rounded-pill ' . $badgeClass . ' text-uppercase shadow-sm" style="min-width: 90px; padding: 6px 0;">' . strtoupper(e($status)) . '</span>';
            })
            ->rawColumns(['checkbox', 'no_srs', 'requester_info', 'sub_category', 'status'])
            ->make(true);
    }

    public function printBatch(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $requisitions = Requisition::with([
            'requester.department',
            'requisitionItems.itemMaster',
            'approvalLogs.approver.roles'
        ])->whereIn('id', $request->input('ids'))->orderBy('no_srs', 'asc')->get();

        if ($requisitions->isEmpty()) {
            return back()->with('error', 'No requisitions selected.');
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
            ->editColumn('log_name', fn($log) => '<span class="badge rounded-pill bg-dark text-white text-uppercase" style="padding: 6px 12px;">FREE GOODS</span>')
            ->editColumn('event', function ($log) {
                $event = strtoupper($log->event);
                $badgeClass = 'status-default'; 
                if (in_array($log->event, ['create', 'created', 'approve', 'approved'])) $badgeClass = 'status-completed'; 
                elseif (in_array($log->event, ['update', 'updated', 'tracking'])) $badgeClass = 'status-processing'; 
                elseif (in_array($log->event, ['delete', 'deleted', 'reject', 'rejected', 'recall', 'recalled'])) $badgeClass = 'status-rejected'; 
                return '<span class="badge rounded-pill ' . $badgeClass . ' text-uppercase shadow-sm" style="min-width: 80px; padding: 6px 0;">' . $event . '</span>';
            })
            ->addColumn('subject_info', fn($log) => ($log->subject && $log->subject->no_srs) ? '<span class="badge-custom badge-fg-no"># ' . e($log->subject->no_srs) . '</span>' : '<span class="badge bg-light text-dark">N/A</span>')
            ->addColumn('subject_id', fn($log) => $log->subject_id)
            ->addColumn('causer_info', function ($log) {
                if ($log->causer && $log->causer->name) {
                    $avatar = $log->causer->avatar ? asset($log->causer->avatar) : asset('assets/images/logo/sinarmeadow.png');
                    return '
                    <div class="badge-requester">
                        <img src="'.$avatar.'" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                        <span>'.e($log->causer->name).'</span>
                    </div>';
                }
                return '<span class="badge bg-secondary text-white">SYSTEM</span>';
            })
            ->editColumn('created_at', fn($log) => Carbon::parse($log->created_at)->format('d M Y, H:i:s'))
            ->rawColumns(['log_name', 'event', 'subject_info', 'causer_info'])
            ->make(true);
    }

    //======================================================================
    // APPROVAL RESPONSE HANDLING
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

        if ($action === 'approve' && !$tracking) {
            $request->merge(['token' => $token, 'action' => 'approve', 'notes' => 'Approved via quick action link.']);
            return $this->processApproval($request);
        }

        $requisition = $approvalLog ? $approvalLog->requisition : $tracking->requisition;
        $requisition->load('requester.department', 'requisitionItems.itemMaster', 'approvalLogs.approver');

        $isWarehouseProcess = (bool)$tracking;
        $pageTitle = $isWarehouseProcess ? ($tracking->current_position ?? 'Warehouse Process') : 'Approval Action';

        if ($action === 'reject') $action = 'review';

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
            $requisition = $approvalLog->requisition->load('requester');
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
                
                if($requisition->requester) {
                    $requisition->requester->notify(new RequisitionNotification([
                        'requisition_id' => $requisition->id,
                        'srs_number'     => $requisition->no_srs,
                        'message'        => "FG Request dari {$requisition->requester->name} telah di-REJECT oleh {$approverName}.",
                        'url'            => route('freegoods-form.index'), 
                    ], $approvalLog->approver));
                }

                $title = 'Requisition Rejected';
                $newStatus = 'Rejected';

            } else {
                if (!str_starts_with($finalNotes, 'Approved by')) {
                    $title = 'Approved with Review';
                    $actionText = 'Approved with Review';
                }

                $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                                ->where('level', '>', $approvalLog->level)
                                                ->orderBy('level', 'asc')->first();

                if ($nextApprovalLog) {
                    // Masih ada Approval Managerial berikutnya
                    $nextApprover = User::where('nik', $nextApprovalLog->approver_nik)->first();
                    if ($nextApprover) {
                        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprover->name]);
                        
                        dispatch(new sendFreeGoods($requisition, $nextApprover, $nextApprovalLog->token, ['mail_type' => 'approval']))
                            ->delay(now()->addSeconds(3));
                        
                        $nextApprover->notify(new RequisitionNotification([
                            'requisition_id' => $requisition->id,
                            'srs_number'     => $requisition->no_srs,
                            'message'        => "FG Request dari {$requisition->requester->name} menunggu approval Anda.",
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
                    // APPROVAL MANAGER SELESAI -> Lanjut ke Warehouse (Outward WH Supervisor)
                    $requisition->update(['status' => 'Approved']);
                    
                    // --- FORCE OUTWARD WH SUPERVISOR FLOW ---
                    $newStatus = $this->handlePostApprovalFlow($requisition);
                }
            }

            activity()
                ->causedBy($approvalLog->approver)
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event($logStatus === 'Rejected' ? 'reject' : 'approve')
                ->log("{$logStatus} Free Goods Requisition");

            DB::commit();

            return redirect()->route('fg.approval.success')
                ->with('card_class', $cardClass)
                ->with('title', $title)
                ->with('message', 'Your response has been successfully recorded.')
                ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->recipient_name ?? 'N/A')
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

            $causer = Auth::user() ?? $this->findUserForStep($tracking->current_position);
            activity()
                ->causedBy($causer)
                ->performedOn($requisition)
                ->useLog('freegoods')
                ->event('tracking')
                ->log("Completed warehouse step: {$tracking->current_position}");

            // Lanjut ke step berikutnya (jika ada) atau Selesai
            $newStatus = $this->advanceWarehouseStep($requisition);

            DB::commit();

            return redirect()->route('fg.approval.success')
                ->with('card_class', 'success')->with('title', 'Warehouse Step Completed')
                ->with('message', 'Warehouse process step has been recorded.')
                ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->recipient_name ?? 'N/A')
                ->with('action_text', $action === 'update_qty' ? 'Qty Updated' : 'Processed')
                ->with('approver_name', $tracking->current_position)
                ->with('new_status', $newStatus);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal melanjutkan proses warehouse Free Goods: " . $e->getMessage());
            return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred. Please check the system logs.');
        }
    }

    /**
     * GENERATE TRACKING PATH (DENGAN MANUAL FALLBACK)
     * Memastikan 'Outward WH Supervisor' ada di path.
     */
    private function handlePostApprovalFlow(Requisition $requisition)
    {
        Log::info("=== START POST APPROVAL (WAREHOUSE) FLOW for SRS: {$requisition->no_srs} ===");

        try {
            // 1. Coba Generate Path dari Database (Trait)
            $this->generateTrackingPath($requisition->id, 'FREE GOODS', $requisition->sub_category);

            // 2. Cek apakah ada Tracking yang terbentuk?
            $trackingCount = Tracking::where('requisition_id', $requisition->id)->count();

            // ================== MANUAL FALLBACK & FORCE ==================
            // Jika tidak ada tracking yang terbentuk otomatis, KITA PAKSA BUAT 'Outward WH Supervisor'
            if ($trackingCount == 0) {
                Log::warning("TIDAK ADA PATH di Database. Membuat langkah Outward WH Supervisor secara MANUAL.");
                
                $stepName = 'Outward WH Supervisor'; 
                
                // Cari User yang namanya ada "Outward" atau "Warehouse"
                $outwardUser = $this->findUserForStep($stepName);
                $approverNik = $outwardUser ? $outwardUser->nik : 'SYS-WH';

                Tracking::create([
                    'requisition_id'    => $requisition->id,
                    'approver_nik'      => $approverNik,
                    'current_position'  => $stepName,
                    'status'            => 'Pending',
                    'token'             => Str::uuid(), // Token manual
                    'last_updated'      => null,
                    'ordering'          => 1,
                ]);
                Log::info("Langkah '{$stepName}' berhasil dibuat secara manual. Assigned to: {$approverNik}");
            }
            // =====================================================

            // 3. Lanjut ke Step Pertama (Entah itu dari DB atau yang barusan kita buat manual)
            return $this->advanceWarehouseStep($requisition);

        } catch (\Exception $e) {
            Log::error("Gagal generate tracking path: " . $e->getMessage());
            // Safety net: kalau error parah, selesaikan aja daripada stuck
            return $this->notifyRequesterAsCompleted($requisition);
        }
    }

    /**
     * ADVANCE WAREHOUSE STEP
     */
    private function advanceWarehouseStep(Requisition $requisition)
    {
        // Ambil step teratas yang belum selesai
        $nextStep = Tracking::where('requisition_id', $requisition->id)
                            ->whereNull('last_updated')
                            ->orderBy('ordering', 'asc') // Pakai ordering atau id
                            ->orderBy('id', 'asc')
                            ->first();

        if ($nextStep) {
            $stepName = $nextStep->current_position;
            $requisition->update(['status' => 'Processing', 'route_to' => $stepName]);

            // Cari User menggunakan fungsi helper cerdas
            $user = User::where('nik', $nextStep->approver_nik)->first();
            if (!$user) {
                // Jika user di DB (NIK) tidak valid, cari lagi berdasarkan nama step
                $user = $this->findUserForStep($stepName);
            }

            if ($user) {
                // Pastikan token ada untuk step ini
                if (!$nextStep->token) {
                    $nextStep->update(['token' => Str::uuid()]);
                }
                
                $baseUrl = route('fg.approval.response', ['token' => $nextStep->token]);
                
                // Dispatch Email dengan DELAY 3 detik
                dispatch(new sendFreeGoods($requisition, $user, $nextStep->token, [
                    'mail_type'    => 'warehouse_process',
                    'process_step' => $stepName,
                    'submit_url'     => $baseUrl . '?action=submit',
                    'review_url'     => $baseUrl . '?action=review',
                    'update_qty_url' => $baseUrl . '?action=update_qty',
                ]))->delay(now()->addSeconds(3));

                Log::info("Email Warehouse untuk step '{$stepName}' didispatch ke user: {$user->name}");
            } else {
                Log::error("CRITICAL: User untuk step '{$stepName}' tidak ditemukan sama sekali.");
                $requisition->update(['route_to' => "Error: User {$stepName} Not Found"]);
            }
            
            return "Processing (" . $stepName . ")";
        } else {
            // Jika benar-benar tidak ada step lagi, baru selesai.
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
            ]))->delay(now()->addSeconds(3));

            $requisition->requester->notify(new RequisitionNotification([
                'requisition_id' => $requisition->id,
                'srs_number'     => $requisition->no_srs,
                'message'        => "FG Request #{$requisition->no_srs} telah SELESAI diproses.",
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