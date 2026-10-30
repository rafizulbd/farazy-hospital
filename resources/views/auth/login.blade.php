@php $setting = \App\Models\SystemSetting::first(); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Sign In | {{ $setting->hospital_name ?? 'Farazy Hospital Ltd.' }}</title>
    @if(!empty($setting->favicon_url)) <link rel="icon" href="{{ $setting->favicon_url }}"> @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-12 border border-slate-200">
        <div class="md:col-span-5 bg-[#064e3b] text-white p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    @if(!empty($setting->logo_url))
                        <img src="{{ $setting->logo_url }}" class="h-11 w-11 rounded-lg bg-white p-1 object-contain shadow">
                    @else
                        <div class="h-11 w-11 rounded-lg bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-xl font-bold text-emerald-300">
                            <i class="fa-regular fa-hospital"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-lg font-bold leading-snug">{{ $setting->hospital_name ?? 'Farazy Hospital Ltd.' }}</h1>
                        <p class="text-xs text-emerald-300 font-medium">{{ $setting->department_name ?? 'Front Desk Department' }}</p>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold leading-tight mt-8">Workforce Roster & Attendance Portal</h2>
                <p class="text-xs text-emerald-100/80 leading-relaxed mt-3">
                    Internal portal for duty roster scheduling, mutual shift exchange approvals, daily time-clock verification, and HR reporting.
                </p>
            </div>
            <div class="mt-8 pt-6 border-t border-emerald-800/80 text-[11px] text-emerald-200/80 space-y-1">
                <div><i class="fa-solid fa-location-dot mr-1.5 text-emerald-400"></i> {{ $setting->hospital_address ?? 'Banasree, Rampura, Dhaka-1219' }}</div>
                <div><i class="fa-solid fa-shield-halved mr-1.5 text-emerald-400"></i> Authorized Staff Access Only</div>
            </div>
        </div>

        <div class="md:col-span-7 p-8 md:p-10 flex flex-col justify-center">
            <div class="mb-6">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md">Staff Portal</span>
                <h3 class="text-2xl font-bold text-slate-900 mt-2">Sign in to your account</h3>
                <p class="text-xs text-slate-500 mt-1">Enter your registered email and password.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 text-emerald-800 font-semibold text-xs bg-emerald-50 border border-emerald-200 p-3 rounded-lg">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-slate-700 font-semibold text-xs mb-1.5">Official Email Address</label>
                    <input type="email" name="email" placeholder="name@farazy.com" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 outline-none text-sm transition" required autofocus>
                    @error('email') <span class="text-rose-600 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-slate-700 font-semibold text-xs">Password</label>
                        <a href="{{ route('password.custom.request') }}" class="text-xs font-semibold text-emerald-700 hover:underline">Forgot password?</a>
                    </div>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 outline-none text-sm transition" required>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center text-xs text-slate-600 font-medium cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-700">
                        <span class="ml-2">Keep me signed in</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#065f46] hover:bg-[#064e3b] text-white font-semibold py-2.5 rounded-lg shadow-sm transition text-sm">
                    Sign In to Portal
                </button>
            </form>
        </div>
    </div>
</body>
</html>
