@if(!empty($setting->favicon_url))
<script>
(function() {
    var l = document.querySelector("link[rel~='icon']") || document.createElement('link');
    l.rel = 'icon'; l.href = "{{ $setting->favicon_url }}"; document.head.appendChild(l);
})();
</script>
@endif
<header class="bg-[#064e3b] text-white shadow-md sticky top-0 z-40 border-b border-emerald-700">
    <div class="max-w-7xl mx-auto px-4 py-2.5 flex flex-wrap justify-between items-center gap-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            @if(!empty($setting->logo_url))
                <img src="{{ $setting->logo_url }}" class="h-9 w-9 rounded-lg bg-white p-1 object-contain shadow-sm">
            @else
                <div class="h-9 w-9 rounded-lg bg-emerald-700 border border-emerald-500 flex items-center justify-center font-bold text-base text-white shadow-sm">
                    <i class="fa-regular fa-hospital"></i>
                </div>
            @endif
            <div>
                <span class="text-base md:text-lg font-bold tracking-tight block leading-none">{{ $setting->hospital_name }}</span>
                <span class="text-[11px] text-emerald-200 font-medium block mt-0.5">{{ $setting->department_name }}</span>
            </div>
        </a>

        <nav class="flex flex-wrap items-center gap-1 md:gap-1.5 text-xs font-semibold">
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-white text-[#064e3b] shadow-sm font-bold' : 'hover:bg-emerald-800 text-emerald-100' }}">
                <i class="fa-solid fa-table-columns"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('rosters.index') }}" class="px-3 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('rosters.*') ? 'bg-white text-[#064e3b] shadow-sm font-bold' : 'hover:bg-emerald-800 text-emerald-100' }}">
                <i class="fa-regular fa-calendar-check"></i> <span>Roster, Swap & In/Out</span>
            </a>
            <a href="{{ route('report.print') }}" class="px-3 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('report.print') ? 'bg-white text-[#064e3b] shadow-sm font-bold' : 'hover:bg-emerald-800 text-emerald-100' }}">
                <i class="fa-solid fa-print"></i> <span>A4 HR Sheet & Archive</span>
            </a>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.panel') }}" class="px-3 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('admin.panel') ? 'bg-amber-400 text-slate-950 shadow-sm font-bold' : 'bg-emerald-950/80 hover:bg-amber-400 hover:text-slate-950 text-amber-300 border border-emerald-700' }}">
                <i class="fa-solid fa-sliders"></i> <span>Admin Control Center</span>
            </a>
            @endif
        </nav>

        <div class="flex items-center gap-2.5">
            <div class="hidden sm:block text-right">
                <div class="text-xs font-bold leading-none">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-emerald-300 mt-0.5">{{ Auth::user()->designation }} ({{ Auth::user()->employee_id }})</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                    <i class="fa-solid fa-power-off"></i> <span class="hidden md:inline ml-1">Sign Out</span>
                </button>
            </form>
        </div>
    </div>

    <div class="bg-[#022c22] text-emerald-100 border-t border-emerald-800/80 px-4 py-2 text-xs">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-2 flex-1">
                <span class="bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded text-[10px] uppercase tracking-wider shrink-0">Notice</span>
                <span class="font-medium text-emerald-50">{{ $setting->notice_text }}</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($setting->is_verified)
                    <span class="bg-emerald-600 text-white px-2.5 py-0.5 rounded-md font-bold text-[11px] border border-emerald-400">
                        <i class="fa-solid fa-circle-check mr-1"></i> Verified Final Roster ({{ $setting->verified_by }})
                    </span>
                @elseif($setting->roster_phase === 'draft_open')
                    <span class="bg-amber-400 text-slate-950 px-2.5 py-0.5 rounded-md font-bold text-[11px]">29th Window: Draft Open</span>
                @elseif($setting->roster_phase === 'merge_review')
                    <span class="bg-sky-400 text-slate-950 px-2.5 py-0.5 rounded-md font-bold text-[11px]">30th Window: Merged & Swap Open</span>
                @else
                    <span class="bg-rose-600 text-white px-2.5 py-0.5 rounded-md font-bold text-[11px]">Roster Window Locked</span>
                @endif
            </div>
        </div>
    </div>
</header>
