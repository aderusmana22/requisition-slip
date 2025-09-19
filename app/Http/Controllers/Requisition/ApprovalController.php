<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Jobs\SendRequisitionApprovalEmail;
use App\Models\Requisition\ApprovalLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    public function approve($token)
    {
        $log = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if (!$log) {
            return view('approvals.invalid', ['message' => 'Link persetujuan ini sudah tidak valid atau sudah diproses.']);
        }

        // Update log saat ini
        $log->status = 'Approved';
        $log->token = null;
        $log->save();

        $requisition = $log->requisition;

        // --- LOGIKA BARU: CARI APPROVER SELANJUTNYA ---
        $nextApprover = null;

        // Jika yang baru approve adalah level 1 (Atasan), cari level 2 (Business Controller)
        if ($log->level == 1) {
            $nextApprover = User::where('role', 'Business Controller')->first(); // Sesuaikan dengan cara Anda menemukan BC
            if($nextApprover) {
                 $requisition->update(['route_to' => 'Business Controller']);
            }
        }

        // Anda bisa menambahkan 'else if ($log->level == 2)' untuk approver selanjutnya

        // Jika approver selanjutnya ditemukan
        if ($nextApprover) {
            $nextApprovalLog = ApprovalLog::create([
                'requisition_id' => $requisition->id,
                'approver_nik'   => $nextApprover->nik,
                'status'         => 'Pending',
                'level'          => $log->level + 1,
                'token'          => Str::uuid()->toString(),
            ]);

            // Kirim email ke approver selanjutnya menggunakan Job yang sama
            SendRequisitionApprovalEmail::dispatch($nextApprover, $requisition, $nextApprovalLog);

        } else {
            // Jika tidak ada approver lagi, proses selesai
            $requisition->update(['status' => 'Completed', 'route_to' => 'Finished']);
        }

        return view('approvals.success', ['message' => 'Requisition telah berhasil Anda setujui.']);
    }

    public function showRejectForm($token)
    {
        $log = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        if (!$log) {
            return view('approvals.invalid', ['message' => 'Link penolakan ini sudah tidak valid.']);
        }
        return view('approvals.reject', ['token' => $token]);
    }

    public function reject(Request $request, $token)
    {
        $request->validate(['notes' => 'required|string|min:10']);
        $log = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        if (!$log) {
            return view('approvals.invalid', ['message' => 'Link penolakan ini sudah tidak valid.']);
        }

        $log->update(['status' => 'Rejected', 'notes' => $request->notes, 'token' => null]);

        // Asumsi ada relasi 'approver' di model ApprovalLog ke User
        $approverName = $log->approver->name ?? 'Approver';
        $log->requisition->update(['status' => 'Rejected', 'route_to' => 'Rejected by ' . $approverName]);

        return view('approvals.success', ['message' => 'Requisition telah berhasil Anda tolak.']);
    }
}
