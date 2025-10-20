<?php

namespace App\Http\Controllers;

use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Menyediakan data agregat untuk kartu metrik di dashboard.
     */
    public function getMetricCounts()
    {
        $counts = Requisition::select(
                DB::raw("SUM(CASE WHEN category = 'SAMPLE' AND sub_category = 'Finished Goods' THEN 1 ELSE 0 END) as sample_fg"),
                DB::raw("SUM(CASE WHEN category = 'SAMPLE' AND sub_category = 'Packaging' THEN 1 ELSE 0 END) as sample_pkg"),
                DB::raw("SUM(CASE WHEN category = 'SAMPLE' AND sub_category = 'Special Order' THEN 1 ELSE 0 END) as sample_so"),
                DB::raw("SUM(CASE WHEN category = 'Complain' THEN 1 ELSE 0 END) as complain"),
                DB::raw("SUM(CASE WHEN category = 'FreeGoods' THEN 1 ELSE 0 END) as free_goods")
            )
            ->first()
            ->toArray();

        return response()->json($counts);
    }

    /**
     * Menyediakan data untuk chart statistik per bulan.
     */
    public function getMonthlyStats(Request $request)
    {
        $year = $request->input('year', now()->year);

        $stats = Requisition::select(
                DB::raw('MONTH(request_date) as month'),
                DB::raw("COUNT(id) as created"),

                // [MODIFIKASI] Approved sekarang hanya menghitung status 'Approved'
                DB::raw("SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved"),

                DB::raw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress"),
                DB::raw("SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected"),

                // [BARU] Tambahkan penghitungan untuk status 'Completed'
                DB::raw("SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed")

            )
            ->whereYear('request_date', $year)
            ->groupBy(DB::raw('MONTH(request_date)'))
            ->orderBy(DB::raw('MONTH(request_date)'), 'ASC')
            ->get();

        $chartData = [
            'created'     => array_fill(0, 12, 0),
            'approved'    => array_fill(0, 12, 0),
            'pending'     => array_fill(0, 12, 0),
            'in_progress' => array_fill(0, 12, 0),
            'rejected'    => array_fill(0, 12, 0),
            'completed'   => array_fill(0, 12, 0), // <-- BARIS BARU
        ];

        foreach ($stats as $stat) {
            $monthIndex = $stat->month - 1;
            $chartData['created'][$monthIndex]     = (int)$stat->created;
            $chartData['approved'][$monthIndex]    = (int)$stat->approved;
            $chartData['pending'][$monthIndex]     = (int)$stat->pending;
            $chartData['in_progress'][$monthIndex] = (int)$stat->in_progress;
            $chartData['rejected'][$monthIndex]    = (int)$stat->rejected;
            $chartData['completed'][$monthIndex]   = (int)$stat->completed; // <-- BARIS BARU
        }

        return response()->json($chartData);
    }

    /**
     * Menyediakan data Top 5 Item yang paling sering direquest.
     */
    public function getTopItems(Request $request)
    {
        $query = DB::table('requisition_items')
            ->join('item_masters', 'requisition_items.item_master_id', '=', 'item_masters.id')
            ->join('requisitions', 'requisition_items.requisition_id', '=', 'requisitions.id')
            ->select('item_masters.item_master_name as name', 'item_masters.item_master_code as sku', DB::raw('COUNT(requisition_items.id) as total'))
            ->groupBy('item_masters.item_master_name', 'item_masters.item_master_code');

        if ($request->filled('category') && $request->category != 'all') {
            if ($request->category == 'sample' || $request->category == 'complain' || $request->category == 'freegood') {
                $query->where('requisitions.category', ucfirst($request->category));
            }
        }
        if ($request->filled('month') && $request->month != 'all') {
            $query->whereMonth('requisitions.request_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('requisitions.request_date', $request->year);
        }

        $topItems = $query->orderBy('total', 'desc')->limit(5)->get();

        return response()->json($topItems);
    }

    /**
     * Menyediakan data Top 5 Customer yang paling sering melakukan request.
     */
    public function getTopCustomers(Request $request)
    {

        $query = DB::table('requisition_items')
            ->join('item_masters', 'requisition_items.item_master_id', '=', 'item_masters.id')
            ->join('requisitions', 'requisition_items.requisition_id', '=', 'requisitions.id')
            ->select('item_masters.item_master_name as name', 'item_masters.item_master_code as sku', DB::raw('COUNT(requisition_items.id) as total'))
            ->groupBy('item_masters.item_master_name', 'item_masters.item_master_code');

        $query = DB::table('requisitions')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->whereNotNull('requisitions.customer_id') // Hanya hitung requisition yang punya
            ->select('customers.name', 'customers.name as code', DB::raw('COUNT(requisitions.id) as total'))
            ->groupBy('customers.name', 'customers.name');

        if ($request->filled('category') && $request->category != 'all') {
             if ($request->category == 'sample' || $request->category == 'complain' || $request->category == 'freegood') {
                $query->where('requisitions.category', ucfirst($request->category));
            }
        }
        if ($request->filled('month') && $request->month != 'all') {
            $query->whereMonth('request_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('request_date', $request->year);
        }

        $topCustomers = $query->orderBy('total', 'desc')->limit(5)->get();

        return response()->json($topCustomers);
    }

    /**
     * Mengambil aktivitas requisition terbaru.
     */
    public function getRecentActivities()
    {
        $user = Auth::user();

        // [FIX] Query diubah untuk mengambil langsung dari tabel requisitions
        $query = Requisition::with('requester:nik,name') // Eager load requester
            ->orderBy('updated_at', 'desc'); // Urutkan berdasarkan update terbaru

        // Jika user bukan super-admin, filter hanya requisition milik user tersebut
        if (!$user->hasRole('super-admin')) {
            $query->where('requester_nik', $user->nik);
        }

        $recentRequisitions = $query->limit(5)->get();

        // Format data agar sesuai dengan yang diharapkan frontend
        $formattedActivities = $recentRequisitions->map(function ($requisition) {
            return [
                'srs_number'     => $requisition->no_srs,
                'requester_name' => optional($requisition->requester)->name ?? 'N/A',
                'category'       => $requisition->sub_category ?? $requisition->category,
                'status'         => $requisition->status,
                'timestamp'      => $requisition->updated_at->diffForHumans(),
            ];
        });

        return response()->json($formattedActivities);
    }

    /**
     * Mengambil notifikasi atau tindakan yang perlu dilakukan user.
     */
    public function getMyActions()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications()->limit(5)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'],
                'url' => $notification->data['url'] ?? '#',
                'timestamp' => $notification->created_at->diffForHumans(),
                'causer_name' => $notification->data['causer_name'] ?? 'System',
            ];
        });

        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'notifications' => $notifications
        ]);
    }
}
