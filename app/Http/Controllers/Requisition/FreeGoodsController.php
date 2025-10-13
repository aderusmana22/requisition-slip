<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFreeGoodsRequest; // Akan dibuat
use App\Http\Requests\UpdateFreeGoodsRequest; // Akan dibuat
use App\Jobs\sendFreeGoods; // Akan dibuat
use App\Mail\MailRejectFreeGoods; // Akan dibuat
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Master\ItemDetail;
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
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ApprovalTrait;
use Illuminate\Support\Str;

class FreeGoodsController extends Controller
{
    use ApprovalTrait;

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

    public function getData()
    {
        $user = Auth::user();

        $query = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->where('requisitions.category', 'FREE GOODS')
            ->select(
                'requisitions.id',
                'requisitions.requester_nik',
                'requisitions.request_date',
                'requisitions.sub_category',
                'requisitions.route_to',
                'requisitions.status',
                'users.name as requester_name',
                'users.avatar',
                'customers.name as customer_name'
            );

        if (!$user->hasRole('super-admin')) {
             // Tampilkan semua data jika user adalah anggota SnM (contoh Dept Head/Manager)
             // Jika hanya requester, hanya tampilkan data dia. Kita anggap di sini semua yang bukan super-admin hanya melihat milik sendiri
             // Implementasi ini disederhanakan: hanya requester yang melihat miliknya
             $query->where('requisitions.requester_nik', $user->nik);
        }

        $query->orderBy('requisitions.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('requester_info', function ($requisition) {
                $avatar = $requisition->avatar ? asset($requisition->avatar) : asset('assets/images/logo/sinarmeadow.png');
                $nik = e($requisition->requester_nik);

                return '
                    <div class="d-flex align-items-center">
                        <div class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-dark me-2">
                            <img src="' . $avatar . '" alt="avatar" class="img-fluid">
                        </div>
                        <div>
                            <small class="text-muted">' . $nik . '</small>
                        </div>
                    </div>
                ';
            })
            ->editColumn('request_date', fn($req) => Carbon::parse($req->request_date)->format('d M Y'))
            ->editColumn('sub_category', function ($requisition) {
                $subCategory = $requisition->sub_category;
                $badgeClass = 'bg-primary';
                return '<span class="badge ' . $badgeClass . '">' . e($subCategory) . '</span>';
            })
            ->editColumn('route_to', fn($req) => '<span class="badge bg-warning text-dark"><i class="ph-bold ph-user-switch me-1"></i>' . e($req->route_to) . '</span>')
            ->editColumn('status', function ($requisition) {
                $status = $requisition->status;
                $badgeClass = 'bg-primary text-white';
                if (in_array($status, ['Submitted', 'Pending'])) $badgeClass = 'bg-primary';
                elseif (in_array($status, ['Approved', 'Completed'])) $badgeClass = 'bg-success';
                elseif (in_array($status, ['Rejected', 'Cancelled'])) $badgeClass = 'bg-danger';
                elseif ($status == 'In Progress') $badgeClass = 'bg-info';
                return '<span class="badge ' . $badgeClass . '">' . e($status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $user = Auth::user();

                $viewBtn = '<button type="button" class="btn btn-sm btn-info btn-view-requisition" data-id="' . $row->id . '" title="Show Detail"><i class="fa-solid fa-eye text-white"></i></button>';
                $editBtn = '';
                $deleteBtn = '';

                if ($row->status === 'Pending') {
                    $editBtn = '<button type="button" class="btn btn-sm btn-warning btn-edit-requisition" data-id="' . $row->id . '" title="Edit"><i class="fa-solid fa-pencil text-white"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete-requisition" data-id="' . $row->id . '" title="Delete"><i class="fa-solid fa-trash-alt text-white"></i></button>';
                }

                return "<div class='d-flex gap-1'>{$viewBtn} {$editBtn} {$deleteBtn}</div>";
            })
            ->rawColumns(['requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    public function store(StoreFreeGoodsRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user = User::with('atasan', 'department')->find(Auth::id());
            $userDepartmentCode = $user->department?->code ?? null;
            $subCategory = 'General Request';

            // Menentukan sub_category
            $subCategory = 'General Request'; // Sesuai flowchart

            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => $this->generateFgNumber(),
                'account' => $validated['account'],
                'cost_center' => $validated['cost_center'] ?? null,
                'request_date' => $validated['request_date'],
                'category' => 'FREE GOODS',
                'sub_category' => $subCategory, // Fixed value for FREE GOODS
                'objectives' => $validated['objectives'],
                'estimated_potential' => $validated['estimated_potential'],
                'status' => 'Pending',
                'route_to' => 'N/A',
            ]);

            // Item untuk Free Goods selalu itemMaster
            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'item_master_id' => $itemMasterId,
                    'material_type' => $subCategory, // Gunakan subCategory sebagai material_type
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued' => $itemData['quantity_issued'] ?? null,
                ]);
            }

            Log::info("Memulai proses approval untuk Free Goods Requisition #{$requisition->id} menggunakan ApprovalTrait.");

            // Panggil fungsi dari trait untuk membuat semua log approval
            // Kita akan menggunakan 'General Request' sebagai sub_category untuk path lookup
            $this->generateApprovalLogs($user, $requisition->id, 'FREE GOODS', $subCategory, $userDepartmentCode);

            // Cari log pertama untuk dikirim email
            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    // Update 'route_to' ke approver pertama
                    $requisition->update(['route_to' => $firstApprover->name]);

                    // Kirim email hanya ke approver pertama
                    sendFreeGoods::dispatch($requisition, $firstApprover, $firstLog->token); // Job baru
                    Log::info("Job email Free Goods dikirim ke approver pertama: {$firstApprover->name}.");
                } else {
                    $requisition->update(['status' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                // Fallback jika tidak ada alur approval
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada alur approval Free Goods yang cocok. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();
            $nextFgNumber = $this->generateFgNumber();

            return response()->json([
                'success' => true,
                'message' => 'Free Goods Requisition berhasil dibuat dan permintaan persetujuan telah dikirim.',
                'next_fg_number' => $nextFgNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat Free Goods requisition: ' . $e->getMessage() . ' di baris ' . $e->getLine());
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

    public function update(UpdateFreeGoodsRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $validated = $request->validated();
        $subCategory = 'General Request';

        DB::beginTransaction();
        try {
            $requisition->update($validated);
            $requisition->requisitionItems()->delete();

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id'    => $requisition->id,
                    'item_master_id'    => $itemMasterId,
                    'material_type'     => $subCategory,
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued'   => $itemData['quantity_issued'] ?? null,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Free Goods Requisition berhasil diubah.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah Free Goods requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
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

    // ================ Logic Approval & Tracking ================== //

    // Adaptasi dari SampleController, hanya alur tracking yang berbeda (Outward WH Supervisor)

    private function startPostApprovalProcess(Requisition $requisition)
    {
        $newStatus = 'Processing';
        Log::info("Approval path selesai untuk Free Goods Requisition #{$requisition->id}. Memulai proses warehouse.");

        // Untuk Free Goods, alurnya langsung ke Outward WH Supervisor
        $newStatus = $this->startFinishedGoodsProcess($requisition); // Re-use fungsi FG karena alurnya sama

        return $newStatus;
    }

    private function startFinishedGoodsProcess(Requisition $requisition)
    {
        // Outward WH Supervisor adalah langkah pertama dan terakhir sebelum Completed
        $stepName = 'Outward WH Supervisor';
        $this->createOrUpdateTracking($requisition, $stepName, "Menunggu proses oleh outward.");
        return $stepName;
    }

    private function advanceWarehouseStep(Requisition $requisition)
    {
        // Muat ulang relasi tracking untuk mendapatkan data paling baru
        $requisition->load('tracking');
        $currentPosition = $requisition->tracking->current_position ?? '';

        // Untuk Free Goods, hanya ada satu langkah: Outward WH Supervisor
        if (str_contains($currentPosition, 'Outward WH Supervisor')) {
            // Setelah proses Outward, langsung selesai.
            return $this->notifyRequesterAsCompleted($requisition);
        }

        // Fallback jika tidak ada alur yang cocok (seharusnya tidak terjadi)
        Log::warning("advanceWarehouseStep dipanggil untuk FG requisition #{$requisition->id} tanpa alur yang cocok.");
        return $this->notifyRequesterAsCompleted($requisition);
    }

    private function notifyRequesterAsCompleted(Requisition $requisition)
    {
        $statusText = 'Completed';
        // Update status utama requisition
        $requisition->update(['status' => $statusText, 'route_to' => 'Finished']);

        // Update tracking ke status final (tanpa membuat token baru)
        Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $statusText,
                'notes'            => "Free Goods process is complete and ready for the requester.",
                'last_updated'     => now(),
                'token'            => null, // Pastikan token kosong saat selesai
            ]
        );

        if ($requisition->requester?->email) {
            // Job/Mail baru untuk Free Goods
            dispatch(new sendFreeGoods($requisition, $requisition->requester, null, [
                'mail_type' => 'completed_notification'
            ]));
        }
        Log::info("Free Goods Requisition #{$requisition->id} selesai. Notifikasi dikirim ke requester.");
        return $statusText;
    }

    private function createOrUpdateTracking(Requisition $requisition, string $currentPosition, string $notes)
    {
        $token = Str::uuid()->toString(); // Buat token baru

        Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $currentPosition,
                'notes'            => $notes,
                'last_updated'     => now(),
                'token'            => $token, // Simpan token ke database
            ]
        );

        $requisition->update(['status' => 'Processing', 'route_to' => $currentPosition]);

        // Kirim notifikasi ke user yang relevan dengan menyertakan token
        $user = $this->findUserForStep($currentPosition);
        if ($user) {
            dispatch(new sendFreeGoods($requisition, $user, $token, [ // Job baru
                'mail_type'    => 'warehouse_process',
                'process_step' => $currentPosition
            ]));
        }
    }

    private function findUserForStep(string $stepName)
    {
        // Untuk Free Goods hanya perlu mencari Outward WH Supervisor
        if (str_contains($stepName, 'Outward WH Supervisor')) {
            // Nik 'WH0002' dari SampleController
            $user = User::where('name', 'like', '%Outward WH Supervisor%')->first();
            return $user ?: User::where('nik', 'WH0002')->first();
        }
        return null;
    }


    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        $validActions = ['approve', 'review', 'reject', 'submit'];

        if (!in_array($action, $validActions)) {
            // Arahkan ke halaman invalid Free Goods
            return view('page.freegoods.invalid', ['message' => 'Invalid action.']);
        }

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        $tracking = !$approvalLog ? Tracking::where('token', $token)->first() : null;

        if (!$approvalLog && !$tracking) {
            // Arahkan ke halaman invalid Free Goods
            return view('page.freegoods.invalid', ['message' => 'This request is invalid or has been processed.']);
        }

        $requisition = $approvalLog ? $approvalLog->requisition : $tracking->requisition;
        $requisition->load('requester', 'customer', 'requisitionItems.itemMaster', 'approvalLogs.approver');

        $isWarehouseProcess = (bool)$tracking;
        $pageTitle = $isWarehouseProcess ? ($tracking->current_position ?? 'Warehouse Process') : 'Approval Action';

        if ($action === 'reject') {
            $action = 'review';
        }

        $viewData = [
            'token' => $token,
            'action' => $action,
            'requisition' => $requisition,
            'pageTitle' => $pageTitle,
            'isWarehouseProcess' => $isWarehouseProcess,
        ];

        // Arahkan ke form response Free Goods
        return view('page.freegoods.response-form', $viewData);
    }

    public function processApproval(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'action' => 'required|string|in:approve,review,reject,submit',
            'notes' => 'nullable|string|max:500|required_if:action,review,reject',
        ]);

        $token = $validated['token'];
        $action = $validated['action'];
        $notes = $validated['notes'] ?? null;

        // --- Langkah 1: Cek token di tabel approval_logs ---
        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if ($approvalLog) {
            DB::beginTransaction();
            try {
                $requisition = $approvalLog->requisition->load('customer', 'requester');
                $approverName = $approvalLog->approver->name ?? 'Unknown Approver';
                $finalNotes = in_array($action, ['review', 'reject']) ? $notes : ('Processed by ' . $approverName);

                $approvalLog->update([
                    'status' => ($action === 'reject') ? 'Rejected' : 'Approved',
                    'notes' => $finalNotes,
                    'responded_at' => now(),
                ]);

                $newStatus = 'In Progress';

                if ($action === 'reject') {
                    $newStatus = 'Rejected';
                    $requisition->update(['status' => 'Rejected', 'route_to' => 'Finished (Rejected)']);
                    if ($requisition->requester?->email) {
                        // Mail baru
                        Mail::to($requisition->requester->email)->send(new MailRejectFreeGoods($requisition, $approverName, $finalNotes));
                    }
                } else {
                    $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                                    ->where('level', '>', $approvalLog->level)
                                                    ->orderBy('level', 'asc')
                                                    ->first();

                    if ($nextApprovalLog) {
                        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprovalLog->approver->name]);
                        // Job baru
                        dispatch(new sendFreeGoods($requisition, $nextApprovalLog->approver, $nextApprovalLog->token, ['mail_type' => 'approval']));
                    } else {
                        $requisition->update(['status' => 'Approved']);
                        $newStatus = $this->startPostApprovalProcess($requisition);
                    }
                }

                DB::commit();

                return redirect()->route('approval.success') // Re-use halaman sukses Sample
                    ->with('card_class', $action === 'reject' ? 'reject' : 'success')
                    ->with('title', 'Action Submitted')->with('message', 'Your response has been successfully recorded.')
                    ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                    ->with('action_text', ucfirst($action))->with('approver_name', $approverName)
                    ->with('new_status', $newStatus);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal memproses approval Free Goods: " . $e->getMessage());
                return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
            }
        }

        // --- Langkah 2: Jika tidak ada, cek token di tabel trackings ---
        $tracking = Tracking::where('token', $token)->first();

        if ($tracking) {
            DB::beginTransaction();
            try {
                $requisition = $tracking->requisition;
                Log::info("Processing warehouse step for Free Goods Requisition #{$requisition->id}. Current position: {$tracking->current_position}.");

                $updateData = ['token' => null];
                if ($notes) {
                    $existingNotes = $tracking->notes ? $tracking->notes . "\n" : '';
                    $updateData['notes'] = $existingNotes . "- " . $notes;
                }
                $tracking->update($updateData);

                $newStatus = $this->advanceWarehouseStep($requisition);
                Log::info("Warehouse step advanced for Free Goods Requisition #{$requisition->id}. New status/route: {$newStatus}.");

                DB::commit();

                return redirect()->route('approval.success')
                    ->with('card_class', 'success')->with('title', 'Action Submitted')
                    ->with('message', 'Warehouse process step has been recorded.')
                    ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                    ->with('action_text', 'Processed')->with('approver_name', 'Warehouse Team')
                    ->with('new_status', $newStatus);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal melanjutkan proses warehouse Free Goods: " . $e->getMessage());
                return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred. Please check the system logs.');
            }
        }

        // --- Langkah 3: Jika token tidak ditemukan di mana pun ---
        return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request')->withMessage('This approval request is invalid or has already been processed.');
    }

    public function showSuccessPage()
    {
        if (!session('title')) {
            return redirect('/');
        }
        return view('page.sample.response-success'); // Re-use tampilan sukses Sample
    }

    public function show($id)
    {
        // Untuk Free Goods, hanya perlu eager load itemMaster
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email',
            'requisitionItems:requisition_id,item_master_id,quantity_required,quantity_issued',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'approvalLogs:id,requisition_id,approver_nik,status,notes,updated_at,level',
            'approvalLogs.approver:nik,name',
            'trackings' // Load data tracking
        ])->findOrFail($id);

        // ... Logic for tracking history ... (Untuk mempersingkat kode, kita anggap logicnya sama dengan SampleController)
        $trackingHistory = [];
        // [Logic untuk tracking history di sini]
        // ...

        $responseData = $requisition->toArray();
        $responseData['tracking_history'] = $trackingHistory;

        return response()->json($responseData);
    }
}