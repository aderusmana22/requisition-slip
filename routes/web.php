<?php

use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Requisition\ComplainController;
use App\Http\Controllers\Requisition\FreeGoodsController;
use App\Http\Controllers\Requisition\SampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);

    Route::resource('sample-form', SampleController::class);
    Route::resource('complain-form', ComplainController::class);
    Route::resource('free-goods', FreeGoodsController::class);

    Route::get('/sample-form/reports', [SampleController::class, 'reports'])->name('sample-form.reports');
    Route::get('/complain-form/reports', [ComplainController::class, 'reports'])->name('complain-form.reports');
    Route::get('/free-goods/reports', [FreeGoodsController::class, 'reports'])->name('free-goods.reports');

});

require __DIR__.'/auth.php';
