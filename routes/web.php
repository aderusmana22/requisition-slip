<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Requisition\ComplainApprovalController;
use App\Http\Controllers\Requisition\ComplainController;
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


// --- Routes for Email Links (Public) ---
Route::get('/approval/response/{token}', [SampleController::class, 'showResponseForm'])->name('approval.response');
Route::get('/approval/success', [SampleController::class, 'showSuccessPage'])->name('approval.success');
Route::post('/approval/process', [SampleController::class, 'processApproval'])->name('approval-sample.process-form');

Route::get('/fg-approval/response/{token}', [FreeGoodsController::class, 'showResponseForm'])->name('fg.approval.response');
Route::get('/fg-approval/success', [FreeGoodsController::class, 'showSuccessPage'])->name('fg.approval.success');
Route::post('/fg-approval/process', [FreeGoodsController::class, 'processApproval'])->name('fg.approval.process');

Route::get('/complain/approval-direct', [ComplainController::class, 'processApproval'])->name('approval.process.direct');
Route::post('/complain/approval/process', [ComplainController::class, 'processApproval'])->name('complain.approval.process');


// --- Main Application Routes (Requires Authentication) ---
Route::middleware('auth')->group(function () {

    Route::prefix('dashboard/data')->name('dashboard.data.')->group(function () {
        Route::get('/metric-counts', [DashboardController::class, 'getMetricCounts'])->name('metric-counts');
        Route::get('/monthly-stats', [DashboardController::class, 'getMonthlyStats'])->name('monthly-stats');
        Route::get('/top-items', [DashboardController::class, 'getTopItems'])->name('top-items');
        Route::get('/top-customers', [DashboardController::class, 'getTopCustomers'])->name('top-customers');
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('recent-activities');
        Route::get('/my-actions', [DashboardController::class, 'getMyActions'])->name('my-actions');
        Route::get('/available-years', [DashboardController::class, 'getAvailableYearsApi'])->name('available-years');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Notifications ---
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
    Route::post('/approvals/resend/{token}', [SampleController::class, 'resendApprovalEmail'])->name('approvals.resend');


    // --- Sample Requisition Routes ---
    Route::get('/sample-form/approval', [SampleController::class, 'approvalPage'])->name('sample-form.approval');
    Route::get('/sample-approval/data', [SampleController::class, 'getApprovalData'])->name('sample.approval.data');
    Route::get('/sample-form/reports', [SampleController::class, 'reportsPage'])->name('sample-form.reports');
    Route::get('/sample-reports/data', [SampleController::class, 'getReportsData'])->name('sample.reports.data');
    Route::get('/sample-report/print-batch', [SampleController::class, 'printReports'])->name('report_sample.print.batch');
    Route::get('/sample-report/{id}', [SampleController::class, 'printReport'])->name('sample.report');
    Route::get('/sample-form/log', [SampleController::class, 'logPage'])->name('sample-form.log');
    Route::get('/sample-log/data', [SampleController::class, 'getLogData'])->name('sample.log.data');
    Route::get('/sample-data', [SampleController::class, 'getData'])->name('sample.data');
    Route::post('/sample-form/{id}/cancel', [SampleController::class, 'cancelRequisition'])->name('sample.cancel');
    Route::post('/get-products-by-material-types', [SampleController::class, 'getProductsByMaterialTypes'])->name('sample.getProductsByMaterialTypes');
    Route::post('/get-item-details-by-products', [SampleController::class, 'getItemDetailsByProducts'])->name('sample.getItemDetailsByProducts');
    Route::get('/get-all-item-masters', [SampleController::class, 'getAllItemMasters'])->name('sample.getAllItemMasters');
    Route::resource('sample-form', SampleController::class);

    // --- Complain Requisition Routes ---
    // [FIX] Menggunakan ComplainApprovalController untuk halaman approval
    Route::resource('/complain-form/approval', ComplainApprovalController::class)->only(['index']);
    Route::get('/getapproverdata/{id?}', [ComplainApprovalController::class, 'getData'])->name('get.approver.data');
    Route::get('/complain-form/reports', [ComplainController::class, 'reports'])->name('complain-form.reports');
    Route::get('/complain-form/log', [ComplainController::class, 'log'])->name('complain-form.log');
    Route::prefix('requisition')->group(function () {
        Route::get('/getComplainData', [ComplainController::class, 'getData'])->name('get.complain.data');
        Route::get('/getCostumerList', [ComplainController::class, 'getCustomerList'])->name('customers.list');
        Route::get('/getSerial', [ComplainController::class, 'getSerial'])->name('get.serial');
        Route::get('/getProductList', [ComplainController::class, 'getProductList'])->name('get.product.list');
        Route::get('/getformdetail/{id}', [ComplainController::class, 'getFormDetail'])->name('get.form.detail');
        Route::post('/upload-payment-proof', [ComplainController::class, 'uploadPaymentProof'])->name('upload.payment.proof');
        Route::get('/complain-report/{id}', [ComplainController::class, 'printReport'])->name('complain.report');
    });
    Route::resource('complain-form', ComplainController::class);

    // --- Free Goods Requisition Routes ---
    Route::get('/freegoods-form/approval', [FreeGoodsController::class, 'approvalPage'])->name('freegoods-form.approval');
    Route::get('/freegoods-form/approval/data', [FreeGoodsController::class, 'getApprovalData'])->name('freegoods.approval.data');
    Route::get('/freegoods-form/reports', [FreeGoodsController::class, 'reportIndex'])->name('freegoods-form.reports');
    Route::get('/freegoods/reports/data', [FreeGoodsController::class, 'getReportData'])->name('freegoods.reports.data');
    Route::post('/freegoods/reports/print-batch', [FreeGoodsController::class, 'printBatch'])->name('freegoods.report.print.batch');
    Route::get('/freegoods-form/log', [FreeGoodsController::class, 'log'])->name('freegoods-form.log');
    Route::get('/freegoods-log/data', [FreeGoodsController::class, 'getLogData'])->name('freegoods.log.data');
    Route::post('/freegoods-form/{id}/recall', [FreeGoodsController::class, 'recallRequisition'])->name('freegoods.recall');
    Route::get('/freegoods-form/get-next-number', [FreeGoodsController::class, 'getNextFgNumber'])->name('freegoods.get-next-number');
    Route::get('/freegoods-data', [FreeGoodsController::class, 'getData'])->name('freegoods.data');
    Route::get('/get-all-item-masters-fg', [FreeGoodsController::class, 'getAllItemMasters'])->name('freegoods.getAllItemMasters');
    Route::resource('freegoods-form', FreeGoodsController::class)->parameters(['freegoods-form' => 'id']);

    // --- Master Management ---
    Route::get('/requistion/path', [RequisitionPath::class, 'index'])->name('requistion.path');
    Route::get('/getapproverlist', [RequisitionPath::class, 'approverList'])->name('get.approverlist');
    Route::resource('/approvers', RequisitionPath::class);
    Route::get('/categories', [RequisitionPath::class, 'categories'])->name('get.categories');
    Route::get('/approver-name', [RequisitionPath::class, 'approverName'])->name('get.approver.name');
});


// --- Admin & Super Admin Routes ---
Route::group(['middleware' => ['role:super-admin|admin']], function () {
    Route::resource('users', UserController::class);
    Route::get('/users-data', [UserController::class, 'getData'])->name('users.data');
    Route::resource('departments', DepartmentController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])->name('roles.give-permissions');
    Route::post('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])->name('roles.give-permission');
});

require __DIR__ . '/auth.php';