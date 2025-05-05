<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\StaffLoginController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\AdminController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\IndexController;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApprovalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 管理者
Route::prefix('admin')->group(function () {
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AdminLoginController::class, 'login']);
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/index', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/detail/{work_id}',[AdminController::class,'detail'])->name('admin.detail');
    Route::put('/edit/{work_id}',[AdminController::class,'edit'])->name('admin.edit');
    Route::get('/edit/{work_id}',[AdminController::class,'edit'])->name('admin.edit');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    Route::get('/staff/index',[AdminController::class,'staffIndex'])->name('staff.index');
    Route::get('/staff/worklog/{staff_id}',[AdminController::class,'staffWorklog'])->name('staff.worklog');
    Route::get('/approval',[ApprovalController::class,'approval'])->name('admin.approval');
    Route::get('/approval/detail/{work_id}',[ApprovalController::class,'approvalDetail'])->name('admin.approval.detail');
    Route::get('/detail/{work_id}/complete',[ApprovalController::class,'approvalComplete'])->name('admin.approval.complete');
    Route::post('/approval/detail/{work_id}', [ApprovalController::class, 'update'])->name('approval.update');
    Route::get('/staff/{staff_id}/export', [AdminController::class, 'export'])->name('staff.export');
});


// スタッフ
Route::prefix('staff')->group(function () {
Route::get('/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
Route::post('/login', [StaffLoginController::class, 'login']);
});

Route::middleware(['auth:staff', 'verified'])->prefix('staff')->group(function () {
    Route::get('/attendance', [WorkController::class, 'attendance'])->name('staff.attendance');
    Route::post('/logout', [StaffLoginController::class, 'logout'])->name('staff.logout');
    Route::get('/work',[WorkController::class,'work'])->name('work');
    Route::post('/work',[WorkController::class,'work'])->name('work');
    Route::get('/index',[IndexController::class,'index'])->name('index');
    Route::post('/index',[IndexController::class,'index'])->name('index');
    Route::get('/detail',[IndexController::class,'detail'])->name('detail');
    Route::get('/detail/{work_id}', [IndexController::class, 'detail'])->name('work.detail');
    Route::post('/detail/{work_id}/edit',[IndexController::class,'edit'])->name('detail.edit');
    Route::get('/detail/{work_id}/approval',[IndexController::class,'approvalDetail'])->name('approval.detail');
    Route::get('/detail/{work_id}/complete',[IndexController::class,'approvalComplete'])->name('approval.complete');
    Route::get('/approval',[IndexController::class,'approval'])->name('approval');
});


Route::get('/register', [StaffLoginController::class, 'register']);
Route::post('/register', [StaffLoginController::class, 'store']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    session()->get('unauthenticated_user')->sendEmailVerificationNotification();
    session()->put('resent', true);
    return back()->with('message', 'Verification link sent!');
})->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    $user = Staff::findOrFail($request->route('id'));
    Auth::guard('staff')->login($user);
    session()->forget('unauthenticated_user');
    return redirect('staff/attendance');
})->middleware(['signed'])->name('verification.verify');
