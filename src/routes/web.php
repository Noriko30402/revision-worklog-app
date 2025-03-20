<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\StaffLoginController;
use App\Http\Controllers\WorkController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;




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
Route::prefix('admin')->name('admin.')->group(function () {
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AdminLoginController::class, 'login']);
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});


// 🚀 スタッフログインルート
Route::prefix('staff')->group(function () {
Route::get('/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
Route::post('/login', [StaffLoginController::class, 'login']);
});

Route::middleware(['auth:staff', 'verified'])->prefix('staff')->group(function () {
    Route::get('/index', [WorkController::class, 'index'])->name('staff.index');
    Route::post('/logout', [WorkController::class, 'logout'])->name('staff.logout');
});


// Route::get('/index', [WorkController::class, 'index'])->name('index');

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
    session()->forget('unauthenticated_user');
    return redirect('/staff/index');
})->middleware('auth:staff')->name('verification.verify');
