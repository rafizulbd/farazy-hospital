@php
    $setting = $setting ?? \App\Models\SystemSetting::first() ?? new \App\Models\SystemSetting([
        'hospital_name' => 'Farazy Hospital Ltd.',
        'department_name' => 'Front Desk & Patient Care Services',
        'hospital_address' => 'House # 15-19, Block-E, Main Road, Banasree, Rampura, Dhaka-1219',
        'notice_text' => 'Submit 7-Day Draft Roster by 29th. Merge & Mutual Swap on 30th. Final Verification on 31st.',
        'roster_phase' => 'draft_open'
    ]);
@endphp

@if(!empty($setting->favicon_url))
<script>
    (function() {
        var link = document.querySelector("link[rel~='icon']") || document.createElement('link');
        link.rel = 'icon';
        link.href = "{{ $setting->favicon_url }}";
        document.head.appendChild(link);
    })();
</script>
@endif

<header class="bg-[#053b2c] text-white shadow-lg sticky top-0 z-50 border-b border-emerald-700/80">
    <!-- Top Emergency Alert Bar (If Active) -->
    @if(!empty($setting->emergency_alert))
    <div class="bg-rose-600 text-white px-4 py-1.5 text-xs font-bold text-center flex items-center justify-center gap-2">
        <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
        <span>{{ $setting->emergency_alert }}</span>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 py-2.5 flex justify-between items-center gap-3">
        <!-- Brand Logo & Hospital Title -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group shrink-0">
            @if(!empty($setting->logo_url))
                <img src="{{ $setting->logo_url }}" alt="Logo" class="h-10 w-10 rounded-xl bg-white p-1 object-contain shadow-md group-hover:scale-105 transition">
            @else
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 border border-emerald-400/50 flex items-center justify-center font-bold text-lg text-white shadow-md">
                    <i class="fa-regular fa-hospital"></i>
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-base md:text-lg font-extrabold tracking-tight leading-none text-white">{{ $setting->hospital_name }}</span>
                    @if($setting->is_verified)
                        <span class="hidden sm:inline-flex items-center gap-1 bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-full" title="Verified by {{ $setting->verified_by }}">
                            <i class="fa-solid fa-circle-check text-emerald-400"></i> Verified
                        </span>
                    @endif
                </div>
                <span class="text-[11px] text-emerald-300 font-medium block mt-0.5">{{ $setting->department_name }}</span>
            </div>
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="hidden lg:flex items-center gap-1.5 text-xs font-semibold bg-[#03261c]/70 p-1 rounded-xl border border-emerald-800/80">
            <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-white text-[#053b2c] shadow font-bold' : 'hover:bg-emerald-800/70 text-emerald-100' }}">
                <i class="fa-solid fa-chart-pie"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('rosters.index') }}" class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('rosters.*') ? 'bg-white text-[#053b2c] shadow font-bold' : 'hover:bg-emerald-800/70 text-emerald-100' }}">
                <i class="fa-regular fa-calendar-check"></i> <span>Roster, Swap & In/Out</span>
            </a>
            <a href="{{ route('report.print') }}" class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('report.print') ? 'bg-white text-[#053b2c] shadow font-bold' : 'hover:bg-emerald-800/70 text-emerald-100' }}">
                <i class="fa-solid fa-print"></i> <span>A4 HR Sheet & CSV</span>
            </a>
            @if(Auth::check() && Auth::user()->role === 'admin')
            <a href="{{ route('admin.panel') }}" class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('admin.panel') ? 'bg-amber-400 text-slate-950 shadow font-bold' : 'bg-amber-500/15 hover:bg-amber-400 hover:text-slate-950 text-amber-300 border border-amber-400/30' }}">
                <i class="fa-solid fa-sliders"></i> <span>Admin Master Panel</span>
            </a>
            @endif
        </nav>

        <!-- Right Side: Live Clock, User Badge & Logout -->
        <div class="flex items-center gap-2.5">
            <!-- Live Clock (Desktop) -->
            <div class="hidden xl:flex items-center gap-1.5 bg-[#03261c] border border-emerald-800 px-3 py-1.5 rounded-lg text-[11px] font-mono text-emerald-200">
                <i class="fa-regular fa-clock text-emerald-400"></i>
                <span id="farazy-live-clock">{{ date('h:i:s A') }}</span>
            </div>

            @if(Auth::check())
            <div class="hidden sm:flex items-center gap-2.5 bg-emerald-900/50 border border-emerald-700/60 px-3 py-1.5 rounded-xl">
                <div class="h-7 w-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="text-right leading-tight">
                    <div class="text-xs font-bold text-white">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-emerald-300">{{ Auth::user()->employee_id ?? 'Staff' }} &bull; {{ ucfirst(Auth::user()->role) }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                @csrf
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-2 rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5" title="Sign Out">
                    <i class="fa-solid fa-power-off"></i> <span class="hidden md:inline">Sign Out</span>
                </button>
            </form>
            @endif

            <!-- Mobile Hamburger Button -->
            <button type="button" onclick="document.getElementById('farazy-mobile-menu').classList.toggle('hidden')" class="lg:hidden bg-emerald-800 hover:bg-emerald-700 text-white p-2 rounded-xl border border-emerald-600">
                <i class="fa-solid fa-bars text-base"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Collapsible Navigation Drawer -->
    <div id="farazy-mobile-menu" class="hidden lg:hidden bg-[#03261c] border-t border-emerald-800 px-4 py-3 space-y-2 text-xs font-semibold">
        @if(Auth::check())
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-emerald-800/80">
            <div>
                <div class="font-bold text-white text-sm">{{ Auth::user()->name }}</div>
                <div class="text-[11px] text-emerald-300">{{ Auth::user()->designation }} ({{ Auth::user()->employee_id }})</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-rose-600 text-white px-3 py-1.5 rounded-lg font-bold">
                    <i class="fa-solid fa-power-off mr-1"></i> Sign Out
                </button>
            </form>
        </div>
        @endif
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white text-[#053b2c] font-bold' : 'text-emerald-100 hover:bg-emerald-800/60' }}">
            <i class="fa-solid fa-chart-pie mr-2"></i> Dashboard & Calendar
        </a>
        <a href="{{ route('rosters.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('rosters.*') ? 'bg-white text-[#053b2c] font-bold' : 'text-emerald-100 hover:bg-emerald-800/60' }}">
            <i class="fa-regular fa-calendar-check mr-2"></i> Roster, Swap & In/Out Portal
        </a>
        <a href="{{ route('report.print') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('report.print') ? 'bg-white text-[#053b2c] font-bold' : 'text-emerald-100 hover:bg-emerald-800/60' }}">
            <i class="fa-solid fa-print mr-2"></i> A4 HR Print Sheet & CSV
        </a>
        @if(Auth::check() && Auth::user()->role === 'admin')
        <a href="{{ route('admin.panel') }}" class="block px-3 py-2 rounded-lg bg-amber-400 text-slate-950 font-bold">
            <i class="fa-solid fa-sliders mr-2"></i> Admin Master Control Center
        </a>
        @endif
    </div>

    <!-- Official Notice Ticker & Roster Phase Bar -->
    <div class="bg-[#021f17] text-emerald-100 border-t border-emerald-800/70 px-4 py-1.5 text-xs">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <span class="bg-amber-400 text-slate-950 font-extrabold px-2 py-0.5 rounded text-[10px] uppercase tracking-wider shrink-0 flex items-center gap-1">
                    <i class="fa-solid fa-bullhorn"></i> Notice
                </span>
                <div class="overflow-hidden whitespace-nowrap w-full">
                    <div class="inline-block animate-[marquee_28s_linear_infinite] hover:[animation-play-state:paused] font-medium text-emerald-50">
                        {{ $setting->notice_text }}
                        @if(!empty($setting->hospital_phone))
                            &nbsp;&bull;&nbsp; Front Desk Helpdesk: {{ $setting->hospital_phone }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($setting->roster_phase === 'draft_open')
                    <span class="bg-amber-400/90 text-slate-950 px-2.5 py-0.5 rounded-full font-bold text-[10px] flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-950 animate-ping"></span> 29th Window: Draft Open
                    </span>
                @elseif($setting->roster_phase === 'merge_review')
                    <span class="bg-sky-400 text-slate-950 px-2.5 py-0.5 rounded-full font-bold text-[10px] flex items-center gap-1">
                        <i class="fa-solid fa-people-arrows"></i> 30th Window: Merge & Swap Open
                    </span>
                @elseif($setting->roster_phase === 'final_verified')
                    <span class="bg-emerald-500 text-white px-2.5 py-0.5 rounded-full font-bold text-[10px] flex items-center gap-1">
                        <i class="fa-solid fa-lock"></i> 31st Window: Final Verified
                    </span>
                @else
                    <span class="bg-rose-600 text-white px-2.5 py-0.5 rounded-full font-bold text-[10px]">
                        Portal Locked
                    </span>
                @endif
            </div>
        </div>
    </div>
</header>

<style>
@keyframes marquee {
    0% { transform: translateX(15%); }
    100% { transform: translateX(-100%); }
}
</style>
<script>
    setInterval(function() {
        var el = document.getElementById('farazy-live-clock');
        if (el) {
            el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
    }, 1000);
</script>
