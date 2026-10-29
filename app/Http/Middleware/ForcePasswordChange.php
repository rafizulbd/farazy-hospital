<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ForcePasswordChange {
    public function handle(Request $request, Closure $next) {
        if (Auth::check() && !Auth::user()->is_password_changed) {
            if (!$request->is('force-password-change') && !$request->is('logout')) {
                return redirect()->route('force.password.change');
            }
        }
        return $next($request);
    }
}
