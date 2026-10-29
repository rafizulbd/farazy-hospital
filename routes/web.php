<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RosterController;
use App\Http\Controllers\OTPAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () { return redirect()->route('login'); });

// Custom OTP Routes
Route::get('/forgot-password-custom', [OTPAuthController::class, 'showForgotForm'])->name('password.custom.request');
Route::post('/forgot-password-custom', [OTPAuthController::class, 'sendOTP'])->name('password.custom.email');
Route::get('/verify-otp', [OTPAuthController::class, 'showVerifyForm'])->name('otp.verify');
Route::post('/verify-otp', [OTPAuthController::class, 'verifyOTP'])->name('otp.verify.post');
Route::get('/reset-password-custom', [OTPAuthController::class, 'showResetForm'])->name('password.custom.reset');
Route::post('/reset-password-custom', [OTPAuthController::class, 'resetPassword'])->name('password.custom.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/force-password-change', function () {
        return view('auth.force-password');
    })->name('force.password.change');

    Route::post('/force-password-change', function (Request $request) {
        $request->validate(['password' => 'required|min:8|confirmed']);
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->is_password_changed = true;
        $user->save();
        return redirect()->route('dashboard');
    })->name('force.password.update');
});

Route::middleware(['auth', 'force_password'])->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::resource('doctors', DoctorController::class);
    Route::resource('rosters', RosterController::class);
});

require __DIR__.'/auth.php';
