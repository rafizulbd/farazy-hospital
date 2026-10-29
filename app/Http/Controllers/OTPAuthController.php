<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OTPAuthController extends Controller {
    public function showForgotForm() {
        return view('auth.forgot-custom');
    }

    public function sendOTP(Request $request) {
        $request->validate(['identifier' => 'required']);
        $identifier = $request->identifier;
        
        $user = User::where('email', $identifier)->orWhere('whatsapp_number', $identifier)->first();
        
        if(!$user) {
            return back()->withErrors(['identifier' => 'No account found with this Email or WhatsApp number.']);
        }
        
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();
        
        if(filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            // Email Sending 
            try {
                Mail::raw("Your Farazy Hospital Reset OTP is: $otp. It is valid for 10 minutes.", function($msg) use ($user) {
                    $msg->to($user->email)->subject('Farazy Hospital - Password Reset OTP');
                });
            } catch (\Exception $e) {
                Log::error('Mail Error: '.$e->getMessage());
            }
        } else {
            // WhatsApp Sending (এখানে পরবর্তীতে আপনার WhatsApp API cURL কোড বসাতে হবে)
            Log::info("WhatsApp API Call -> Sent OTP $otp to $user->whatsapp_number");
        }
        
        session(['reset_user_id' => $user->id]);
        return redirect()->route('otp.verify')->with('success', 'OTP has been sent to your Email/WhatsApp.');
    }

    public function showVerifyForm() {
        if(!session('reset_user_id')) return redirect()->route('password.custom.request');
        return view('auth.verify-otp');
    }

    public function verifyOTP(Request $request) {
        $request->validate(['otp' => 'required|numeric']);
        $user = User::find(session('reset_user_id'));
        
        if(!$user || $user->otp !== $request->otp || now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }
        
        session(['otp_verified' => true]);
        return redirect()->route('password.custom.reset');
    }

    public function showResetForm() {
        if(!session('otp_verified')) return redirect()->route('password.custom.request');
        return view('auth.reset-custom');
    }

    public function resetPassword(Request $request) {
        $request->validate(['password' => 'required|min:8|confirmed']);
        $user = User::find(session('reset_user_id'));
        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->is_password_changed = true; // OTP দিয়ে রিসেট করলে আবার ফোর্সের দরকার নেই
        $user->save();
        
        session()->forget(['reset_user_id', 'otp_verified']);
        return redirect()->route('login')->with('status', 'Password reset successfully! Please log in with your new password.');
    }
}
