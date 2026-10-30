<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontDeskPortalController as FD;
use App\Http\Controllers\OTPAuthController as OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/forgot-password-custom', [OTP::class, 'showForgotForm'])->name('password.custom.request');
Route::post('/forgot-password-custom', [OTP::class, 'sendOTP'])->name('password.custom.email');
Route::get('/verify-otp', [OTP::class, 'showVerifyForm'])->name('otp.verify');
Route::post('/verify-otp', [OTP::class, 'verifyOTP'])->name('otp.verify.post');
Route::get('/reset-password-custom', [OTP::class, 'showResetForm'])->name('password.custom.reset');
Route::post('/reset-password-custom', [OTP::class, 'resetPassword'])->name('password.custom.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/force-password-change', fn() => view('auth.force-password'))->name('force.password.change');
    Route::post('/force-password-change', function (Request $r) {
        $r->validate(['password' => 'required|min:8|confirmed']);
        Auth::user()->update(['password' => Hash::make($r->password), 'is_password_changed' => true]);
        return redirect()->route('dashboard');
    })->name('force.password.update');
});

Route::middleware(['auth', 'force_password'])->group(function () {
    Route::get('/dashboard', [FD::class, 'dashboard'])->name('dashboard');
    Route::get('/rosters', [FD::class, 'rosterPortal'])->name('rosters.index');
    Route::get('/rosters/create', [FD::class, 'rosterPortal'])->name('rosters.create');
    Route::post('/rosters/draft', [FD::class, 'saveDraft'])->name('rosters.draft.save');
    Route::post('/rosters/merge', [FD::class, 'mergeAndGenerateMonth'])->name('rosters.merge');
    Route::post('/rosters/unlock/{userId}', [FD::class, 'unlockOrDeleteDraft'])->name('rosters.draft.unlock');
    Route::post('/rosters/swap-request', [FD::class, 'requestSwap'])->name('rosters.swap.request');
    Route::post('/rosters/swap-respond/{id}', [FD::class, 'respondSwap'])->name('rosters.swap.respond');
    Route::post('/attendance/punch/{id}', [FD::class, 'punchAttendance'])->name('attendance.punch');
    Route::post('/attendance/request-update/{id}', [FD::class, 'requestAttendanceUpdate'])->name('attendance.request.update');
    Route::post('/attendance/admin-action/{id}', [FD::class, 'adminApproveAttendance'])->name('attendance.admin.action');
    Route::get('/report/print', [FD::class, 'printReport'])->name('report.print');
    Route::get('/report/export-csv', [FD::class, 'exportCsv'])->name('report.csv');
    Route::get('/admin-panel', [FD::class, 'adminPanel'])->name('admin.panel');
    Route::get('/doctors', [FD::class, 'adminPanel'])->name('doctors.index');
    Route::post('/admin/settings', [FD::class, 'updateSystemSettings'])->name('admin.settings.update');
    Route::post('/admin/users', [FD::class, 'storeUser'])->name('admin.users.store');
    Route::post('/admin/users/{id}/update', [FD::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/{id}/delete', [FD::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/shifts', [FD::class, 'storeShift'])->name('admin.shifts.store');
    Route::post('/admin/shifts/{id}/delete', [FD::class, 'deleteShift'])->name('admin.shifts.delete');
});

require __DIR__.'/auth.php';
