<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Requisition\ComplainApprovalController;
use App\Http\Controllers\Requisition\ComplainController;
use App\Http\Controllers\Requisition\ComplainLogController;
use App\Http\Controllers\Requisition\FreeGoodsController;
use App\Http\Controllers\Requisition\RequisitionPath;
use App\Http\Controllers\Requisition\SampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('/complain-form/approval', ComplainApprovalController::class)->only(['index']);
Route::get('/getapproverdata/{id?}', [ComplainApprovalController::class, 'getData'])->name('get.approver.data');

Route::get('/complain/log/data', [ComplainLogController::class, 'getData'])->name('complain.log.data');

// ! route email complain approval dan warehouse
Route::get('/complain/approval-direct', [ComplainController::class, 'processApproval'])->name('approval.process.direct');
Route::get('/complain/approval/review', [ComplainController::class, 'showReviewPage'])->name('complain.approval.review');
Route::post('/complain/approval/process', [ComplainController::class, 'processApproval'])->name('complain.approval.process');
Route::get('/complain/warehouse/approval', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.approval');
Route::get('/complain/warehouse/review', [ComplainController::class, 'showWarehouseReviewPage'])->name('complain.warehouse.review');
Route::post('/complain/warehouse/process', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.process');
// =====

//! pindahkan ke midddleware approver untuk approvalpath
Route::get('/getapproverlist', [RequisitionPath::class, 'approverList'])->name('get.approverlist');
Route::resource('/approvers', RequisitionPath::class);
Route::get('/categories', [RequisitionPath::class, 'categories'])->name('get.categories');
Route::get('/approver-name', [RequisitionPath::class, 'approverName'])->name('get.approver.name');

Route::get('/approval/response/{token}', [SampleController::class, 'showResponseForm'])->name('approval.response');
Route::post('/approvals/resend/{token}', [SampleController::class, 'resendApprovalEmail'])->name('approvals.resend');
Route::post('/approval/process', [SampleController::class, 'processApproval'])->name('approval-sample.process-form');
Route::get('/approval/success', [SampleController::class, 'showSuccessPage'])->name('approval.success');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Requisition Routes
    Route::get('/sample-form/approval', [SampleController::class, 'approvalPage'])->name('sample-form.approval');
    Route::get('/sample-approval/data', [SampleController::class, 'getApprovalData'])->name('sample.approval.data');

    Route::get('/sample-form/reports', [SampleController::class, 'reportsPage'])->name('sample-form.reports');
    Route::get('/sample-reports/data', [SampleController::class, 'getReportsData'])->name('sample.reports.data');
    Route::get('/sample-report/print-batch', [SampleController::class, 'printReports'])->name('report_sample.print.batch');
    Route::get('/sample-report/{id}', [SampleController::class, 'printReport'])->name('sample.report');

    Route::get('/sample-form/log', [SampleController::class, 'logPage'])->name('sample-form.log');
    Route::get('/sample-log/data', [SampleController::class, 'getLogData'])->name('sample.log.data');

    Route::resource('sample-form', SampleController::class);

    Route::get('/sample-data', [SampleController::class, 'getData'])->name('sample.data');
    Route::post('/sample-form/{id}/cancel', [SampleController::class, 'cancelRequisition'])->name('sample.cancel');
    Route::post('/get-products-by-material-types', [SampleController::class, 'getProductsByMaterialTypes'])->name('sample.getProductsByMaterialTypes');
    Route::post('/get-item-details-by-products', [SampleController::class, 'getItemDetailsByProducts'])->name('sample.getItemDetailsByProducts');
    Route::get('/get-all-item-masters', [SampleController::class, 'getAllItemMasters'])->name('sample.getAllItemMasters');

    Route::resource('complain-form', ComplainController::class);
    Route::resource('free-goods', FreeGoodsController::class);

    // * route complain form
    Route::get('/approval', [ComplainController::class, 'processApproval'])->name('approval.process');
    Route::get('/complain/test-data', [ComplainController::class, 'testData'])->name('complain.test.data');
    Route::get('/complain/test-warehouse/{id}', [ComplainController::class, 'testWarehouseTracking'])->name('complain.test.warehouse');

    // * route log complain
    Route::get('/complain/log',[ComplainLogController::class, 'index'])->name('complain.log.index');

    Route::prefix('requisition')->group(function () {
        Route::get('/getComplainData', [ComplainController::class, 'getData'])->name('get.complain.data');
        Route::get('/getCostumerList', [ComplainController::class, 'getCustomerList'])->name('customers.list');
        Route::get('/getSerial', [ComplainController::class, 'getSerial'])->name('get.serial');
        Route::get('/getProductList', [ComplainController::class, 'getProductList'])->name('get.product.list');
        Route::get('/getformdetail/{id}', [ComplainController::class, 'getFormDetail'])->name('get.form.detail');
        Route::get('/complain-report/{id}', [ComplainController::class, 'printReport'])->name('complain.report');
        Route::post('/upload-payment-proof', [ComplainController::class, 'uploadPaymentProof'])->name('upload.payment.proof');
    });
    //*  ===== terakhir dari complain ====

    Route::get('/sample-form/reports', [SampleController::class, 'reports'])->name('sample-form.reports');
    Route::get('/complain-form/reports', [ComplainController::class, 'reports'])->name('complain-form.reports');
    Route::get('/free-goods/reports', [FreeGoodsController::class, 'reports'])->name('free-goods.reports');

    Route::get('/sample-form/approval', [SampleController::class, 'approval'])->name('sample-form.approval');
    Route::get('/free-goods/approval', [FreeGoodsController::class, 'approval'])->name('free-goods.approval');

     Route::get('/sample-form/log', [SampleController::class, 'log'])->name('sample-form.log');
    Route::get('/free-goods/log', [FreeGoodsController::class, 'log'])->name('free-goods.log');

    //! pindahkan ke midddleware approver
    Route::get('/getapproverlist', [RequisitionPath::class, 'approverList'])->name('get.approverlist');
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

    Route::get('/requistion/path', [RequisitionPath::class, 'index'])->name('requistion.path');

});


require __DIR__ . '/auth.php';
