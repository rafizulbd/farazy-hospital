<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex font-sans">
    <!-- Sidebar -->
    <div class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl min-h-screen">
        <div class="p-6 text-xl font-extrabold border-b border-slate-800 flex items-center gap-2 text-emerald-400">
            <i class="fa-solid fa-hospital"></i> Farazy Hospital
        </div>
        <nav class="flex-1 p-4 space-y-2 mt-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 bg-blue-600 text-white rounded-xl font-semibold shadow">
                <i class="fa-solid fa-calendar-check"></i> Dashboard & Calendar
            </a>
            <a href="{{ route('rosters.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition">
                <i class="fa-solid fa-clock-rotate-left"></i> Roster & Swap Portal
            </a>
            <a href="{{ route('doctors.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition">
                <i class="fa-solid fa-user-doctor"></i> Doctor Directory
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <div class="mb-3 px-2 text-xs text-slate-400">
                Logged in as: <strong class="text-white block text-sm">{{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</strong>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-rose-600 py-2 rounded-xl hover:bg-rose-700 transition font-bold text-sm shadow">
                    <i class="fa-solid fa-right-from-bracket mr-1"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- TOP OFFICIAL NOTICE & TIMELINE BANNER -->
        @if($setting)
        <div class="bg-gradient-to-r from-blue-950 via-indigo-900 to-slate-900 text-white p-6 shadow-lg border-b-4 border-amber-400">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-3">
                    <div class="flex items-center gap-3">
                        <span class="bg-amber-400 text-slate-950 font-extrabold px-3 py-1 rounded-full text-xs uppercase tracking-wider animate-pulse">
                            <i class="fa-solid fa-bullhorn mr-1"></i> Official Notice
                        </span>
                        <h1 class="text-xl font-extrabold tracking-wide">{{ $setting->title }}</h1>
                        <span class="bg-emerald-500/20 border border-emerald-400 text-emerald-300 text-xs font-bold px-3 py-1 rounded-full">
                            <i class="fa-solid fa-shield-halved mr-1"></i> {{ $setting->validity_label }}
                        </span>
                    </div>
                    <div>
                        @if($setting->current_phase === 'draft_open')
                            <span class="bg-blue-500 text-white px-4 py-1.5 rounded-full text-xs font-extrabold uppercase shadow"><i class="fa-solid fa-pen-to-square mr-1"></i> Phase 1: Draft Submission Open</span>
                        @elseif($setting->current_phase === 'swap_open')
                            <span class="bg-amber-500 text-slate-950 px-4 py-1.5 rounded-full text-xs font-extrabold uppercase shadow"><i class="fa-solid fa-people-arrows mr-1"></i> Phase 2: Merge & Mutual Swap Open</span>
                        @else
                            <span class="bg-emerald-500 text-white px-4 py-1.5 rounded-full text-xs font-extrabold uppercase shadow"><i class="fa-solid fa-lock mr-1"></i> Phase 3: Final Approved & Locked ({{ $setting->approved_by }})</span>
                        @endif
                    </div>
                </div>

                <p class="text-amber-200 text-sm font-medium bg-white/10 p-3 rounded-xl border border-white/15 mb-4">
                    {{ $setting->notice_text }}
                </p>

                <!-- 3-Step Visual Timeline -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-xl border {{ $setting->current_phase === 'draft_open' ? 'bg-blue-600/40 border-blue-400 ring-2 ring-blue-400' : 'bg-white/5 border-white/10 opacity-75' }}">
                        <div class="font-extrabold text-blue-300 uppercase">{{ $setting->phase1_date }}</div>
                        <div class="text-white font-semibold mt-0.5">Step 1: 7-Day Draft Roster Entry (Auto Self-Lock)</div>
                    </div>
                    <div class="p-3 rounded-xl border {{ $setting->current_phase === 'swap_open' ? 'bg-amber-500/30 border-amber-400 ring-2 ring-amber-400' : 'bg-white/5 border-white/10 opacity-75' }}">
                        <div class="font-extrabold text-amber-300 uppercase">{{ $setting->phase2_date }}</div>
                        <div class="text-white font-semibold mt-0.5">Step 2: Admin Merge & Mutual Conflict Swap Requisition</div>
                    </div>
                    <div class="p-3 rounded-xl border {{ $setting->current_phase === 'locked_final' ? 'bg-emerald-600/40 border-emerald-400 ring-2 ring-emerald-400' : 'bg-white/5 border-white/10 opacity-75' }}">
                        <div class="font-extrabold text-emerald-300 uppercase">{{ $setting->phase3_date }}</div>
                        <div class="text-white font-semibold mt-0.5">Step 3: Higher Authority Approval & Final Lock</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <main class="p-6 max-w-7xl mx-auto w-full space-y-6">
            @php
                $wRoster = $activeDoctor ? $activeDoctor->weeklyRoster : null;
                $dayMap = ['Sun'=>'sun_shift','Mon'=>'mon_shift','Tue'=>'tue_shift','Wed'=>'wed_shift','Thu'=>'thu_shift','Fri'=>'fri_shift','Sat'=>'sat_shift'];
                $yesterdayDay = date('D', strtotime('-1 day'));
                $todayDay = date('D');
                $tomorrowDay = date('D', strtotime('+1 day'));

                $yesterdayShift = $wRoster ? $wRoster->{$dayMap[$yesterdayDay]} : 'Not Set';
                $todayShift = $wRoster ? $wRoster->{$dayMap[$todayDay]} : 'Not Set';
                $tomorrowShift = $wRoster ? $wRoster->{$dayMap[$tomorrowDay]} : 'Not Set';

                function shiftColorClass($shift) {
                    if (str_contains($shift, 'Morning')) return 'bg-emerald-100 text-emerald-900 border-emerald-400';
                    if (str_contains($shift, 'Evening')) return 'bg-amber-100 text-amber-900 border-amber-400';
                    if (str_contains($shift, 'Night')) return 'bg-indigo-100 text-indigo-900 border-indigo-400';
                    if (str_contains($shift, 'Off')) return 'bg-rose-100 text-rose-800 border-rose-300';
                    return 'bg-slate-100 text-slate-600 border-slate-300';
                }
            @endphp

            <!-- Top Action & Doctor Selector Bar -->
            <div class="bg-white p-4 rounded-2xl shadow-sm flex flex-wrap justify-between items-center gap-4 border">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">
                        <i class="fa-solid fa-calendar-day text-blue-600 mr-1"></i>
                        Live Duty Schedule: <span class="text-blue-700">{{ $activeDoctor ? $activeDoctor->name : 'No Doctor Selected' }}</span>
                    </h2>
                    <p class="text-xs text-slate-500">Each shift is 8 Hours. Weekly 7-day pattern automatically repeats across the entire month.</p>
                </div>
                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <label class="text-xs font-bold text-slate-600">View Schedule For:</label>
                        <select name="doctor_id" onchange="this.form.submit()" class="border border-slate-300 rounded-xl px-3 py-2 text-sm font-semibold bg-slate-50">
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ ($activeDoctor && $activeDoctor->id == $doc->id) ? 'selected' : '' }}>
                                    {{ $doc->name }} ({{ $doc->specialization ?: 'General' }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('rosters.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-sm shadow">
                        <i class="fa-solid fa-sliders mr-1"></i> Go to Roster Portal
                    </a>
                </div>
            </div>

            <!-- Yesterday, Today & Tomorrow Highlight Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-slate-400">
                    <span class="text-xs font-bold uppercase text-slate-400">Yesterday ({{ date('d M, l', strtotime('-1 day')) }})</span>
                    <div class="mt-2 text-lg font-extrabold text-slate-700">{{ $yesterdayShift }}</div>
                    <p class="text-xs text-slate-500 mt-1">Completed 8-Hour Duty Cycle</p>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-lg border-2 border-blue-600 ring-4 ring-blue-50 relative">
                    <span class="absolute top-3 right-3 bg-blue-600 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase animate-bounce">Today's Duty</span>
                    <span class="text-xs font-extrabold uppercase text-blue-600">{{ date('d M Y, l') }}</span>
                    <div class="mt-2 text-2xl font-black text-slate-900">{{ $todayShift }}</div>
                    <p class="text-xs font-semibold text-emerald-700 mt-1"><i class="fa-solid fa-clock mr-1"></i> Active 8-Hour Shift Window</p>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-indigo-500">
                    <span class="text-xs font-bold uppercase text-indigo-600">Tomorrow ({{ date('d M, l', strtotime('+1 day')) }})</span>
                    <div class="mt-2 text-lg font-extrabold text-slate-800">{{ $tomorrowShift }}</div>
                    <p class="text-xs text-slate-500 mt-1">Upcoming 8-Hour Duty Cycle</p>
                </div>
            </div>

            <!-- Interactive Color-Coded Full Monthly Calendar -->
            @php
                $month = $setting ? $setting->active_month : date('n');
                $year = $setting ? $setting->active_year : date('Y');
                $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
                $daysInMonth = date('t', $firstDayOfMonth);
                $startDayOfWeek = date('w', $firstDayOfMonth); // 0 (Sun) to 6 (Sat)
                $monthName = date('F Y', $firstDayOfMonth);
            @endphp

            <div class="bg-white p-6 rounded-2xl shadow-md border">
                <div class="flex flex-wrap justify-between items-center mb-6 gap-4 border-b pb-4">
                    <div>
                        <h3 class="text-xl font-black text-slate-800">
                            <i class="fa-solid fa-calendar-days text-blue-600 mr-2"></i>{{ $monthName }} — Monthly Color-Coded Duty Calendar
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Status: <strong>{{ $setting && $setting->current_phase === 'locked_final' ? '✅ Final Verified & Locked by Authority' : '⏳ Draft / Swap Phase in Progress' }}</strong>
                        </p>
                    </div>
                    <!-- Color Legend -->
                    <div class="flex flex-wrap gap-2 text-xs font-bold">
                        <span class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-400">🌅 Morning (8AM-4PM)</span>
                        <span class="px-3 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-400">🌇 Evening (4PM-12AM)</span>
                        <span class="px-3 py-1 rounded-lg bg-indigo-100 text-indigo-900 border border-indigo-400">🌙 Night (12AM-8AM)</span>
                        <span class="px-3 py-1 rounded-lg bg-rose-100 text-rose-800 border border-rose-300">🏖️ Off Day</span>
                    </div>
                </div>

                <!-- Calendar Grid Header -->
                <div class="grid grid-cols-7 gap-2 text-center font-extrabold text-xs uppercase text-slate-600 mb-2">
                    <div class="bg-slate-100 py-2 rounded-lg">Sunday</div>
                    <div class="bg-slate-100 py-2 rounded-lg">Monday</div>
                    <div class="bg-slate-100 py-2 rounded-lg">Tuesday</div>
                    <div class="bg-slate-100 py-2 rounded-lg">Wednesday</div>
                    <div class="bg-slate-100 py-2 rounded-lg">Thursday</div>
                    <div class="bg-slate-100 py-2 rounded-lg">Friday</div>
                    <div class="bg-slate-100 py-2 rounded-lg">Saturday</div>
                </div>

                <!-- Calendar Days Grid -->
                <div class="grid grid-cols-7 gap-2">
                    @for($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="h-24 bg-slate-50 rounded-xl border border-dashed border-slate-200"></div>
                    @endfor

                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentTimestamp = mktime(0, 0, 0, $month, $day, $year);
                            $dayShort = date('D', $currentTimestamp);
                            $shiftCol = $dayMap[$dayShort];
                            $shiftVal = $wRoster ? $wRoster->$shiftCol : 'No Roster';
                            $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                            $isPast = ($currentTimestamp < strtotime('today'));
                        @endphp
                        <div class="h-28 p-2.5 rounded-xl border flex flex-col justify-between transition hover:scale-[1.02] {{ shiftColorClass($shiftVal) }} {{ $isToday ? 'ring-4 ring-blue-600 shadow-lg' : '' }} {{ $isPast ? 'opacity-75' : '' }}">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-black {{ $isToday ? 'bg-blue-600 text-white px-2 py-0.5 rounded-full' : '' }}">
                                    {{ $day }} {{ date('M', $currentTimestamp) }}
                                </span>
                                <span class="text-[10px] font-bold uppercase opacity-75">{{ $dayShort }}</span>
                            </div>
                            <div class="text-xs font-extrabold leading-tight my-auto">
                                {{ $shiftVal }}
                            </div>
                            <div class="text-[10px] font-semibold flex justify-between items-center opacity-80">
                                <span>8 Hrs</span>
                                @if($isToday)
                                    <span class="font-black text-blue-800 uppercase">● Today</span>
                                @elseif($isPast)
                                    <span>Passed</span>
                                @else
                                    <span>Upcoming</span>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </main>
    </div>
</body>
</html>
