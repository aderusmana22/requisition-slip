<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\Tracking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplainApprovalController extends Controller
{
    //
    public function index()
    {
        return view('page.approval_complain.index');
    }

    public function getData($id = null)
    {
        try{
            $user = Auth::user();

            // Ambil semua requisition dengan kategori 'Complain'
            $complainIds = Requisition::where('category', 'Complain')->pluck('id');

            if ($complainIds->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada requisition complain dalam sistem.',
                    'data' => []
                ], 200);
            }

            $data = ApprovalLog::whereIn('requisition_id', $complainIds)
                ->where('approver_nik', $user->nik)
                ->whereNotNull('token')
                ->where(function($query) {
                
                    $query->whereRaw('level = (
                        SELECT MIN(level) 
                        FROM approval_logs a2 
                        WHERE a2.requisition_id = approval_logs.requisition_id 
                        AND a2.token IS NOT NULL
                    )');
                })
                ->with([
                    'requisition' => function($query) {
                        $query->with(['customer', 'requester']);
                    }
                ])
                ->get();

            if ($data->isEmpty()) {
                $whpath = 
                $tracking = Tracking::whereIn('requisition_id', $complainIds)
                    ->where('current_position', $user->nik)
                    ->whereNotNull('token')
                    ->with([
                        'requisition' => function ($query) {
                            $query->with(['customer', 'requester']);
                        }
                    ])
                    ->first();

                if ($tracking) {
                    $requisition = $tracking->requisition;
                    return response()->json([
                        'message' => 'Ada requisition complain yang menunggu approval dari Anda.',
                        'data' => [
                            'tracking_id' => $tracking->id,
                            'requisition_id' => $tracking->requisition_id,
                            'requisition_number' => $requisition->no_srs,
                            'status' => $requisition->status,
                            'updated_at' => $requisition->updated_at,
                            'token' => $tracking->token,
                            'requisition_detail'=>[
                                'requester_name' => $requisition->requester->name ?? 'N/A',
                            ]
                        ]
                    ], 200);
                }

                return response()->json([
                    'message' => 'Tidak ada requisition complain yang menunggu approval dari Anda.',
                    'data' => []
                ], 200);
            }

            // Format data untuk response
            $formattedData = $data->map(function ($approval) {
                $requisition = $approval->requisition;
                
                return [
                    'id' => $approval->id,
                    'requisition_id' => $approval->requisition_id,
                    'requisition_number' => $requisition->no_srs ?? null,
                    'approver_nik' => $approval->approver_nik,
                    'level' => $approval->level,
                    'status' => $approval->status,
                    'token' => $approval->token,
                    'updated_at' => $approval->updated_at,
                    'requisition_details' => [
                        'id' => $requisition->id ?? null,
                        'requisition_number' => $requisition->no_srs ?? null,
                        'customer_name' => $requisition->customer->name ?? 'N/A',
                        'requester_name' => $requisition->requester->name ?? 'N/A',
                        'customer_id' => $requisition->customer_id ?? null,
                        'category' => $requisition->category ?? null,
                        'created_at' => $requisition->created_at ?? null,
                        'status' => $requisition->status ?? null,
                    ]
                ];
            });

            return response()->json([
                'message' => 'Data approval complain berhasil ditemukan.',
                'data' => $formattedData,
                'total' => $formattedData->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
