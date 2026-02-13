<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\RevisionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\TrackingPathController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Requisition\ComplainApprovalController;
use App\Http\Controllers\Requisition\ComplainController;
use App\Http\Controllers\Requisition\ComplainLogController;
use App\Http\Controllers\Requisition\FreeGoodsController;
use App\Http\Controllers\Requisition\RequisitionPath;
use App\Http\Controllers\Requisition\SampleController;
use App\Models\Requisition\Requisition;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/dashboard', function () {
    $firstRequisition = Requisition::oldest()->first();
    $firstYear = $firstRequisition ? Carbon::parse($firstRequisition->created_at)->year : Carbon::now()->year;
    $currentYear = Carbon::now()->year;
    $availableYears = range($currentYear, $firstYear);
    return view('dashboard', compact('availableYears'));
})->middleware(['auth', 'verified'])->name('dashboard');

// --- Halaman Error Testing ---
Route::get('/tes-404', function () { abort(404); });
Route::get('/tes-500', function () { abort(500); });
Route::get('/tes-403', function () { abort(403, 'Akses Ditolak'); });

// --- Mailing dan Approval Proses Complain ---
Route::get('/approval', [ComplainController::class, 'processApproval'])->name('approval.process');
Route::get('/complain/approval-direct', [ComplainController::class, 'processApproval'])->name('approval.process.direct');
Route::get('/complain/approval/review', [ComplainController::class, 'showReviewPage'])->name('complain.approval.review');
Route::post('/complain/approval/process', [ComplainController::class, 'processApproval'])->name('complain.approval.process');
Route::get('/complain/warehouse/approval', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.approval');
Route::get('/complain/warehouse/review', [ComplainController::class, 'showWarehouseReviewPage'])->name('complain.warehouse.review');
Route::post('/complain/warehouse/process', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.process');
Route::get('/complain/warehouse/update', [ComplainController::class, 'showWarehouseUpdatePage'])->name('complain.warehouse.update');
Route::post('/complain/warehouse/update', [ComplainController::class, 'updateWarehouseApproval'])->name('complain.warehouse.update.process');
Route::get('/complain/warehouse/report/{id}', [ComplainController::class, 'warehouseReport'])->name('complain.warehouse.report');
Route::get('/complain/public-upload/{id}', [ComplainController::class, 'showPublicPaymentUpload'])->name('complain.public.upload.view');
Route::post('/complain/public-upload/{id}', [ComplainController::class, 'storePublicPaymentProof'])->name('complain.public.upload.store');


// --- Approval Link dari Email (Sample Requisition) ---
Route::get('/approval/response/{token}', [SampleController::class, 'showResponseForm'])->name('approval.response');
Route::post('/approvals/resend/{token}', [SampleController::class, 'resendApprovalEmail'])->name('approvals.resend');
Route::post('/approval/process', [SampleController::class, 'processApproval'])->name('approval-sample.process-form');
Route::get('/approval/success', [SampleController::class, 'showSuccessPage'])->name('approval.success');
Route::get('/requisition/print-email/{id}', [SampleController::class, 'printReportByEmail'])->name('approval.download.pdf');

// --- Approval Link dari Email (Free Goods Requisition) ---
Route::get('/fg-approval/response/{token}', [FreeGoodsController::class, 'showResponseForm'])->name('fg.approval.response');
Route::post('/fg-approval/process', [FreeGoodsController::class, 'processApproval'])->name('fg.approval.process');
Route::get('/fg-approval/success', [FreeGoodsController::class, 'showSuccessPage'])->name('fg.approval.success');

Route::middleware('auth')->group(function () {
    Route::prefix('dashboard/data')->name('dashboard.data.')->group(function () {
        Route::get('/metric-counts', [DashboardController::class, 'getMetricCounts'])->name('metric-counts');
        Route::get('/monthly-stats', [DashboardController::class, 'getMonthlyStats'])->name('monthly-stats');
        Route::get('/top-items', [DashboardController::class, 'getTopItems'])->name('top-items');
        Route::get('/top-customers', [DashboardController::class, 'getTopCustomers'])->name('top-customers');
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('recent-activities');
        Route::get('/my-actions', [DashboardController::class, 'getMyActions'])->name('my-actions');
        Route::get('/available-years', [DashboardController::class, 'getAvailableYearsApi'])->name('available-years');
        Route::get('/incomplete', [DashboardController::class, 'getIncompleteRequisitions'])->name('incomplete');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROUTE NOTIFIKASI
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');

    // ROUTE MASTER TRACKING PATH
    Route::get('/master/tracking-path', [TrackingPathController::class, 'index'])->name('tracking-path.index');
    Route::get('/master/tracking-path/list', [TrackingPathController::class, 'approverList'])->name('tracking-path.list');
    Route::post('/master/tracking-path', [TrackingPathController::class, 'store'])->name('tracking-path.store');
    Route::get('/master/tracking-path/{id}/edit', [TrackingPathController::class, 'edit'])->name('tracking-path.edit');
    Route::put('/master/tracking-path/{id}', [TrackingPathController::class, 'update'])->name('tracking-path.update');
    Route::delete('/master/tracking-path/{id}', [TrackingPathController::class, 'destroy'])->name('tracking-path.destroy');
    Route::get('/master/tracking-path/categories', [TrackingPathController::class, 'categories'])->name('tracking-path.categories');
    Route::get('/master/tracking-path/approver-name', [TrackingPathController::class, 'approverName'])->name('tracking-path.approverName');


    // --- SAMPLE REQUISITION ROUTES ---
    Route::get('/sample-form/approval', [SampleController::class, 'approvalPage'])->name('sample-form.approval');
    Route::get('/sample-approval/data', [SampleController::class, 'getApprovalData'])->name('sample.approval.data');

    Route::get('/sample-form/reports', [SampleController::class, 'reportsPage'])->name('sample-form.reports');
    Route::get('/sample-reports/data', [SampleController::class, 'getReportsData'])->name('sample.reports.data');
    Route::post('/sample-report/print', [SampleController::class, 'printMultipleReport'])->name('report_sample.print');

    Route::get('/sample-form/log', [SampleController::class, 'logPage'])->name('sample-form.log');
    Route::get('/sample-log/data', [SampleController::class, 'getLogData'])->name('sample.log.data');

    Route::resource('sample-form', SampleController::class);

    Route::get('/sample-data', [SampleController::class, 'getData'])->name('sample.data');
    Route::post('/sample-form/{id}/recall', [SampleController::class, 'recallRequisition'])->name('sample.recall');
    Route::post('/get-products-by-material-types', [SampleController::class, 'getProductsByMaterialTypes'])->name('sample.getProductsByMaterialTypes');
    Route::post('/get-item-details-by-products', [SampleController::class, 'getItemDetailsByProducts'])->name('sample.getItemDetailsByProducts');
    Route::get('/get-all-item-masters', [SampleController::class, 'getAllItemMasters'])->name('sample.getAllItemMasters');


    // --- FREE GOODS REQUISITION ROUTES ---
    // [UPDATE] Menggunakan grup 'freegoods-form' dan 'freegoods' sesuai kode awal Anda

    // 1. FORM CRUD (Menggunakan freegoods-form)
    Route::prefix('freegoods-form')->name('freegoods-form.')->group(function () {
        Route::get('/', [FreeGoodsController::class, 'index'])->name('index');

        // Halaman Approval & Report & Log (dashboard)
        Route::get('/approval', [FreeGoodsController::class, 'approvalPage'])->name('approval');
        Route::get('/reports', [FreeGoodsController::class, 'reports'])->name('reports');
        Route::get('/log', [FreeGoodsController::class, 'log'])->name('log');

        Route::post('/{id}/recall', [FreeGoodsController::class, 'recallRequisition'])->name('recall');

        // Resource controller untuk create, update, delete, show
        Route::resource('/', FreeGoodsController::class)->except(['index'])->parameters(['' => 'id']);
    });

    // 2. DATA TABLES & AJAX (Menggunakan freegoods.)
    Route::name('freegoods.')->group(function () {
        // Data Utama
        Route::get('/freegoods-data', [FreeGoodsController::class, 'getData'])->name('data');

        // [FIX ERROR] Route ini yang sebelumnya 'not defined', sekarang sudah ada:
        Route::get('/freegoods-approval/data', [FreeGoodsController::class, 'getApprovalData'])->name('approval.data');

        Route::get('/freegoods-reports/data', [FreeGoodsController::class, 'getReportData'])->name('reports.data');
        Route::get('/freegoods-log/data', [FreeGoodsController::class, 'getLogData'])->name('log.data');

        Route::get('/freegoods/get-next-number', [FreeGoodsController::class, 'getNextFgNumber'])->name('get-next-number');
        Route::get('/get-all-item-masters-fg', [FreeGoodsController::class, 'getAllItemMasters'])->name('getAllItemMasters');

        // Action Process (Print)
        Route::post('/freegoods-form/reports/print-batch', [FreeGoodsController::class, 'printBatch'])->name('report.print.batch');

        // Action Process (Approval POST) - Dipanggil dari modal Approval
        Route::post('/freegoods-approval/process', [FreeGoodsController::class, 'processApproval'])->name('approval.process');
    });


    // --- COMPLAIN ROUTES ---
    Route::prefix('complain')->group(function () {
        Route::resource('complain-form', ComplainController::class);

        Route::get('/getSerial', [ComplainController::class, 'getSerial'])->name('get.serial');
        Route::get('/getStatusFilter', [ComplainController::class, 'statusFilter'])->name('get.status.filter');
        Route::get('/getComplainData', [ComplainController::class, 'getData'])->name('get.complain.data');
        Route::get('/getProductList', [ComplainController::class, 'getProductList'])->name('get.product.list');
        Route::get('/getCostumerList', [ComplainController::class, 'getCustomerList'])->name('customers.list');
        Route::get('/getformdetail/{id}', [ComplainController::class, 'getFormDetail'])->name('get.form.detail');
        Route::post('/upload-payment-proof', [ComplainController::class, 'uploadPaymentProof'])->name('upload.payment.proof');

        Route::get('/test-warehouse/{id}', [ComplainController::class, 'testWarehouseTracking'])->name('complain.test.warehouse');

        Route::get('/approval', [ComplainApprovalController::class, 'index'])->name('complain.approval');
        Route::get('/getapproverdata/{id?}', [ComplainApprovalController::class, 'getData'])->name('get.approver.data');
        Route::post('/approval/resend/{token}', [ComplainApprovalController::class, 'resendApprovalEmail'])->name('complain.approval.resend');

        Route::get('/reports', [ComplainController::class, 'reports'])->name('complain.reports');
        Route::post('/report/print-bulk', [ComplainController::class, 'printBulkReport'])->name('report.print.bulk');

        Route::get('/log', [ComplainLogController::class, 'index'])->name('complain.log');
        Route::get('/log.data', [ComplainLogController::class, 'getData'])->name('complain.log.data');
    });

});


Route::group(['middleware' => ['role:super-admin|admin']], function () {

    Route::resource('users', UserController::class);
    Route::get('/users-data', [UserController::class, 'getData'])->name('users.data');
    Route::resource('departments', DepartmentController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])->name('roles.give-permissions');
    Route::post('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])->name('roles.give-permission');

    // --- Requisition Path (Approvers) ---
    Route::get('/requisition/path', [RequisitionPath::class, 'index'])->name('requisition.path');
    Route::get('/getapproverlist', [RequisitionPath::class, 'approverList'])->name('get.approverlist');
    Route::resource('/approvers', RequisitionPath::class);
    Route::get('/categories', [RequisitionPath::class, 'categories'])->name('get.categories');
    Route::get('/approver-name', [RequisitionPath::class, 'approverName'])->name('get.approver.name');

    Route::get('/master/tracking-path', [TrackingPathController::class, 'index'])->name('master.tracking-path.index');
    Route::put('/master/tracking-path/', [TrackingPathController::class, 'edit'])->name('master.tracking-path.update');

    Route::get('/master/revision', [RevisionController::class, 'index'])->name('master.revision.index');
    Route::post('/master/revision/update', [RevisionController::class, 'update'])->name('master.revision.update');
    Route::get('/master/revision/getdata', [RevisionController::class, 'getrevisiondata'])->name('master.revision.getdata');

});
require __DIR__ . '/auth.php';
