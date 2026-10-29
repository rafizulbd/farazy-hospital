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
    Route::get('/dashboard', function (Request $request) {
        $setting = \App\Models\RosterSetting::first();
        $doctors = \App\Models\Doctor::with('weeklyRoster')->get();
        $user = Auth::user();
        $selectedDocId = $request->query('doctor_id', $user->doctor_id ?: ($doctors->first()->id ?? null));
        $activeDoctor = $selectedDocId ? \App\Models\Doctor::with('weeklyRoster')->find($selectedDocId) : null;
        return view('dashboard', compact('setting', 'doctors', 'activeDoctor'));
    })->name('dashboard');

    Route::resource('doctors', DoctorController::class);

    Route::get('/rosters', [RosterController::class, 'index'])->name('rosters.index');
    Route::get('/rosters/create', [RosterController::class, 'index'])->name('rosters.create');
    Route::post('/rosters/settings', [RosterController::class, 'updateSettings'])->name('rosters.settings');
    Route::post('/rosters/draft', [RosterController::class, 'saveDraft'])->name('rosters.draft.save');
    Route::post('/rosters/unlock/{doctorId}', [RosterController::class, 'unlockDraft'])->name('rosters.draft.unlock');
    Route::post('/rosters/swap-request', [RosterController::class, 'requestSwap'])->name('rosters.swap.request');
    Route::post('/rosters/swap-respond/{id}', [RosterController::class, 'respondSwap'])->name('rosters.swap.respond');
});

require __DIR__.'/auth.php';
