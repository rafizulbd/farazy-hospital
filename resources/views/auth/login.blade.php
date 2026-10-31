@php
    $setting = \App\Models\SystemSetting::first() ?? new \App\Models\SystemSetting([
        'hospital_name' => 'Farazy Hospital Ltd.',
        'hospital_tagline' => 'Committed to Excellence in Healthcare',
        'department_name' => 'Front Desk & Patient Care Services',
        'hospital_address' => 'House # 15-19, Block-E, Main Road, Banasree, Rampura, Dhaka-1219',
        'hospital_phone' => '+880 1711-000000',
        'hospital_email' => 'frontdesk@farazyhospital.com',
        'validity_text' => 'Official Workforce Duty Roster Portal'
    ]);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Sign In | {{ $setting->hospital_name }}</title>
    @if(!empty($setting->favicon_url))
        <link rel="icon" href="{{ $setting->favicon_url }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col justify-between p-4 md:p-8 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:20px_20px]">
    <div class="my-auto max-w-5xl w-full mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-slate-200/90">
        <!-- Left Corporate Branding Showcase -->
        <div class="lg:col-span-6 bg-gradient-to-br from-[#064e3b] via-[#053b2c] to-[#022c22] text-white p-8 md:p-10 flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3.5 mb-8">
                    @if(!empty($setting->logo_url))
                        <img src="{{ $setting->logo_url }}" alt="Hospital Logo" class="h-14 w-14 rounded-2xl bg-white p-1.5 object-contain shadow-lg">
                    @else
                        <div class="h-14 w-14 rounded-2xl bg-white/10 border border-emerald-400/30 flex items-center justify-center text-2xl font-bold text-emerald-300 shadow-lg">
                            <i class="fa-regular fa-hospital"></i>
                        </div>
                    @endif
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-300 block">{{ $setting->hospital_tagline ?? 'Enterprise Healthcare Portal' }}</span>
                        <h1 class="text-xl md:text-2xl font-extrabold tracking-tight leading-snug">{{ $setting->hospital_name }}</h1>
                        <p class="text-xs text-emerald-200/90 font-semibold">{{ $setting->department_name }}</p>
                    </div>
                </div>

                <div class="space-y-4 my-8">
                    <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 px-3 py-1 rounded-full text-xs font-bold">
                        <i class="fa-solid fa-Calendar-check text-emerald-300"></i>
                        <span>{{ $setting->validity_text }}</span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-extrabold leading-tight text-white">
                        Front Desk Duty Roster & Attendance Management
                    </h2>
                    <p class="text-xs md:text-sm text-emerald-100/80 leading-relaxed">
                        Authorized enterprise system for 7-day draft roster scheduling (6 Days Duty + 1 Day Off), 2-stage mutual shift swap requisitions, daily time-clock verification, and 1-page A4 HR reporting.
                    </p>
                </div>

                <!-- Feature Highlights -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="bg-white/5 border border-white/10 p-3 rounded-xl">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-400 text-sm mb-1"></i>
                        <div class="text-xs font-bold">Auto-Locked Punch</div>
                        <div class="text-[10px] text-emerald-200/70">Daily In/Out & Overtime</div>
                    </div>
                    <div class="bg-white/5 border border-white/10 p-3 rounded-xl">
                        <i class="fa-solid fa-people-arrows text-amber-400 text-sm mb-1"></i>
                        <div class="text-xs font-bold">2-Stage Shift Swap</div>
                        <div class="text-[10px] text-emerald-200/70">Colleague & Admin Verified</div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-8 pt-5 border-t border-emerald-800/80 text-[11px] text-emerald-200/80 space-y-1.5">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-location-dot text-emerald-400 mt-0.5"></i>
                    <span>{{ $setting->hospital_address }}</span>
                </div>
                @if(!empty($setting->hospital_phone) || !empty($setting->hospital_email))
                <div class="flex flex-wrap gap-4 pt-1">
                    @if(!empty($setting->hospital_phone))
                        <span><i class="fa-solid fa-phone text-emerald-400 mr-1"></i> {{ $setting->hospital_phone }}</span>
                    @endif
                    @if(!empty($setting->hospital_email))
                        <span><i class="fa-regular fa-envelope text-emerald-400 mr-1"></i> {{ $setting->hospital_email }}</span>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Right Login Form Panel -->
        <div class="lg:col-span-6 p-8 md:p-12 flex flex-col justify-center bg-white">
            <div class="mb-7">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#065f46] bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                        <i class="fa-solid fa-shield-halved mr-1"></i> Staff Authentication
                    </span>
                    <span class="text-xs font-semibold text-slate-400">{{ date('d M Y') }}</span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-3">Sign in to your account</h3>
                <p class="text-xs text-slate-500 mt-1">Enter your official credentials to access your duty schedule and time-clock.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 text-emerald-900 font-semibold text-xs bg-emerald-50 border border-emerald-300 p-3.5 rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 text-rose-900 font-semibold text-xs bg-rose-50 border border-rose-300 p-3.5 rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold text-xs mb-1.5">Official Email Address</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="name@farazy.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#065f46] focus:border-[#065f46] outline-none text-sm font-medium transition" required autofocus>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-slate-700 font-bold text-xs">Password</label>
                        <a href="{{ route('password.custom.request') }}" class="text-xs font-bold text-[#065f46] hover:underline">
                            Forgot Password / OTP Reset?
                        </a>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input type="password" id="login-password" name="password" placeholder="••••••••" class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#065f46] focus:border-[#065f46] outline-none text-sm font-medium transition" required>
                        <button type="button" onclick="toggleLoginPassword()" class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-700 text-sm">
                            <i id="eye-icon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center text-xs text-slate-600 font-semibold cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#065f46] focus:ring-[#065f46] h-4 w-4">
                        <span class="ml-2">Keep me signed in on this device</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#065f46] hover:bg-[#053b2c] text-white font-bold py-3 rounded-xl shadow-md hover:shadow-lg transition text-sm flex items-center justify-center gap-2 mt-2">
                    <span>Access Front Desk Portal</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <div class="mt-8 pt-5 border-t border-slate-100 flex justify-between items-center text-[11px] text-slate-400">
                <span><i class="fa-solid fa-headset mr-1 text-emerald-700"></i> Need account access? Contact Front Desk In-Charge</span>
                <span class="font-semibold text-slate-500">v2.5 Enterprise</span>
            </div>
        </div>
    </div>

    <script>
        function toggleLoginPassword() {
            var input = document.getElementById('login-password');
            var icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }
    </script>
</body>
</html>
