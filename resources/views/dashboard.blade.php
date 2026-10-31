@php
    $handoverNotes = $handoverNotes ?? collect();
    $todayTeamRosters = $todayTeamRosters ?? collect();
    $attendanceRate = $kpi['regular_days'] > 0 ? round(($kpi['present_days'] / $kpi['regular_days']) * 100) : 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | {{ $setting->hospital_name }}</title>
    @if(!empty($setting->favicon_url))
        <link rel="icon" href="{{ $setting->favicon_url }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">
    @include('partials.nav')

    <main class="max-w-7xl mx-auto w-full px-4 py-6 space-y-6 flex-1">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-[#065f46] text-emerald-950 font-semibold rounded-2xl shadow-sm text-xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-[#065f46] text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <span class="text-[10px] font-bold uppercase text-emerald-700">Confirmed</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-rose-50 border-l-4 border-rose-600 text-rose-950 font-semibold rounded-2xl shadow-sm text-xs flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if($pendingSwapsCount > 0)
            <div class="p-4 bg-purple-50 border border-purple-300 text-purple-950 rounded-2xl flex flex-wrap justify-between items-center gap-3 shadow-sm">
                <div class="text-xs font-bold flex items-center gap-2.5">
                    <span class="h-3 w-3 rounded-full bg-purple-600 animate-ping"></span>
                    <span>You have <strong>{{ $pendingSwapsCount }} Mutual Shift Swap Request(s)</strong> from a colleague waiting for your response!</span>
                </div>
                <a href="{{ route('rosters.index') }}#swap-section" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    Review & Respond &rarr;
                </a>
            </div>
        @endif

        @if(Auth::user()->role === 'admin' && ($adminPendingSwaps > 0 || $adminPendingUpdates > 0))
            <div class="p-4 bg-amber-50 border border-amber-300 text-amber-950 rounded-2xl flex flex-wrap justify-between items-center gap-3 shadow-sm">
                <div class="text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-amber-600 text-base"></i>
                    <span>In-Charge Approval Needed: <strong>{{ $adminPendingSwaps }} Shift Swap(s)</strong> and <strong>{{ $adminPendingUpdates }} Time Correction(s)</strong> are pending.</span>
                </div>
                <a href="{{ route('admin.panel') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2 rounded-xl text-xs font-extrabold shadow-sm transition">
                    Open Admin Command Center &rarr;
                </a>
            </div>
        @endif

        <!-- Top Executive Profile & Filter Bar -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-200 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3.5">
                <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-[#065f46] to-[#042f23] text-white flex items-center justify-center font-extrabold text-lg shadow-md">
                    {{ substr($activeUser->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-base md:text-lg font-extrabold text-slate-900">{{ $activeUser->name }}</h1>
                        <span class="bg-emerald-50 border border-emerald-200 text-[#065f46] text-[11px] font-bold px-2.5 py-0.5 rounded-full">
                            {{ $activeUser->employee_id }}
                        </span>
                        @if($setting->is_verified)
                            <span class="bg-emerald-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-circle-check"></i> Verified Final Schedule
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $activeUser->designation }} &bull; {{ $activeUser->department }} &bull; <span class="font-semibold text-slate-700">{{ $setting->validity_text }}</span>
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2 text-xs">
                <div class="flex items-center bg-slate-50 border border-slate-300 rounded-xl px-2.5 py-1.5">
                    <i class="fa-regular fa-user text-slate-400 mr-1.5"></i>
                    <select name="user_id" onchange="this.form.submit()" class="font-bold bg-transparent text-slate-800 outline-none">
                        @foreach($team as $member)
                            <option value="{{ $member->id }}" {{ $activeUser->id == $member->id ? 'selected' : '' }}>
                                {{ $member->name }} ({{ $member->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <select name="month" onchange="this.form.submit()" class="border border-slate-300 rounded-xl px-3 py-2 font-bold bg-slate-50 text-slate-800">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>

                <select name="year" onchange="this.form.submit()" class="border border-slate-300 rounded-xl px-3 py-2 font-bold bg-slate-50 text-slate-800">
                    @for($y = 2025; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <a href="{{ route('report.print', ['user_id' => $activeUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-[#065f46] hover:bg-[#053b2c] text-white px-3.5 py-2 rounded-xl font-bold shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> <span>A4 Sheet</span>
                </a>
                <a href="{{ route('report.csv', ['user_id' => $activeUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-slate-800 hover:bg-slate-900 text-white px-3 py-2 rounded-xl font-bold shadow-sm transition flex items-center gap-1">
                    <i class="fa-solid fa-file-csv"></i> <span class="hidden sm:inline">CSV</span>
                </a>
            </form>
        </div>

        <!-- 6 Monthly Performance KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Scheduled Duty</span>
                    <span class="text-2xl font-extrabold text-slate-900 mt-0.5 block">{{ $kpi['regular_days'] }} <small class="text-[11px] font-normal text-slate-500">days</small></span>
                </div>
                <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-emerald-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-emerald-700 uppercase block">Present Logged</span>
                        <span class="text-2xl font-extrabold text-emerald-800 mt-0.5 block">{{ $kpi['present_days'] }} <small class="text-[11px] font-normal text-slate-500">({{ $attendanceRate }}%)</small></span>
                    </div>
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-rose-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-rose-700 uppercase block">Weekly Off</span>
                    <span class="text-2xl font-extrabold text-rose-800 mt-0.5 block">{{ $kpi['off_days'] }} <small class="text-[11px] font-normal text-slate-500">days</small></span>
                </div>
                <div class="h-9 w-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-amber-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-amber-700 uppercase block">Late Count</span>
                    <span class="text-2xl font-extrabold text-amber-700 mt-0.5 block">{{ $kpi['late_days'] }} <small class="text-[11px] font-normal text-slate-500">days</small></span>
                </div>
                <div class="h-9 w-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-indigo-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-indigo-700 uppercase block">Overtime</span>
                    <span class="text-2xl font-extrabold text-indigo-800 mt-0.5 block">{{ $kpi['ot_hours'] }} <small class="text-[11px] font-normal text-slate-500">hrs</small></span>
                </div>
                <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-purple-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-purple-700 uppercase block">Swapped Duty</span>
                    <span class="text-2xl font-extrabold text-purple-800 mt-0.5 block">{{ $kpi['swapped_days'] }} <small class="text-[11px] font-normal text-slate-500">days</small></span>
                </div>
                <div class="h-9 w-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-people-arrows"></i>
                </div>
            </div>
        </div>

        <!-- Today's Live Time-Clock & Overtime Console -->
        @if($todayDuty && ($activeUser->id === Auth::id() || Auth::user()->role === 'admin'))
        <div class="bg-gradient-to-r from-[#053b2c] to-[#065f46] text-white p-5 rounded-3xl shadow-md border border-emerald-600 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3.5">
                <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-2xl text-emerald-300">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded">Live Time-Clock</span>
                        <span class="text-xs font-semibold text-emerald-200">{{ date('l, d F Y') }}</span>
                    </div>
                    <h3 class="text-base md:text-lg font-extrabold mt-0.5">
                        Today's Assigned Shift: <span class="text-amber-300">{{ $todayDuty->shift_name }}</span>
                    </h3>
                    <p class="text-[11px] text-emerald-100/80">In-Time and Out-Time lock automatically upon saving. Use Roster Portal for correction requests.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($todayDuty->is_off_day)
                    <span class="bg-rose-500/30 border border-rose-300/40 text-white px-4 py-2 rounded-xl text-xs font-bold">
                        <i class="fa-solid fa-mug-hot mr-1"></i> Today is Scheduled Weekly Off Day
                    </span>
                @else
                    @if(!$todayDuty->in_locked)
                    <form action="{{ route('attendance.punch', $todayDuty->id) }}" method="POST" class="flex items-center gap-1.5 bg-white/10 p-1.5 rounded-xl border border-white/20" onsubmit="return confirm('Confirm Save In-Time? Once saved, it will lock immediately.');">
                        @csrf
                        <input type="hidden" name="punch_type" value="in_time">
                        <input type="time" name="in_time" value="{{ date('H:i') }}" class="bg-white text-slate-900 rounded-lg px-2.5 py-1.5 text-xs font-extrabold outline-none" required>
                        <button type="submit" class="bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-extrabold px-3.5 py-1.5 rounded-lg text-xs shadow transition">
                            <i class="fa-solid fa-right-to-bracket mr-1"></i> Punch In
                        </button>
                    </form>
                    @else
                    <div class="bg-white/10 border border-emerald-400/40 px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-lock text-emerald-300"></i>
                        <span>In: <strong>{{ $todayDuty->in_time }}</strong> ({{ $todayDuty->attendance_status }})</span>
                    </div>
                    @endif

                    @if(!$todayDuty->out_locked)
                    <form action="{{ route('attendance.punch', $todayDuty->id) }}" method="POST" class="flex items-center gap-1.5 bg-white/10 p-1.5 rounded-xl border border-white/20" onsubmit="return confirm('Confirm Save Out-Time? Once saved, it will lock immediately.');">
                        @csrf
                        <input type="hidden" name="punch_type" value="out_time">
                        <input type="time" name="out_time" value="{{ date('H:i') }}" class="bg-white text-slate-900 rounded-lg px-2.5 py-1.5 text-xs font-extrabold outline-none" required>
                        <button type="submit" class="bg-amber-400 hover:bg-amber-300 text-slate-950 font-extrabold px-3.5 py-1.5 rounded-lg text-xs shadow transition">
                            <i class="fa-solid fa-right-from-bracket mr-1"></i> Punch Out
                        </button>
                    </form>
                    @else
                    <div class="bg-white/10 border border-amber-400/40 px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-lock text-amber-300"></i>
                        <span>Out: <strong>{{ $todayDuty->out_time }}</strong></span>
                    </div>
                    @endif
                @endif

                <a href="{{ route('rosters.index', ['user_id' => $activeUser->id, 'month' => $month, 'year' => $year]) }}#daily-attendance" class="bg-white/15 hover:bg-white/25 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition">
                    Full Log & Overtime &rarr;
                </a>
            </div>
        </div>
        @endif

        <!-- 4-Day Live Duty Snapshot Cards -->
        <div>
            <div class="flex items-center justify-between mb-2.5">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                    <i class="fa-solid fa-bolt text-amber-500"></i>
                    4-Day Live Duty Timeline (Last 2 Days, Today & Tomorrow)
                </h2>
                <a href="{{ route('rosters.index') }}#swap-section" class="text-xs font-bold text-[#065f46] hover:underline">
                    <i class="fa-solid fa-people-arrows mr-1"></i> Request Shift Swap
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $timelineCards = [
                        ['label' => '2 Days Ago', 'date' => \Carbon\Carbon::today()->subDays(2), 'duty' => $twoDaysAgoDuty, 'border' => 'border-slate-200 bg-white', 'badge' => 'bg-slate-100 text-slate-600'],
                        ['label' => 'Yesterday', 'date' => \Carbon\Carbon::today()->subDay(), 'duty' => $yesterdayDuty, 'border' => 'border-slate-300 bg-white', 'badge' => 'bg-slate-200 text-slate-800'],
                        ['label' => 'Today (Active)', 'date' => \Carbon\Carbon::today(), 'duty' => $todayDuty, 'border' => 'border-[#065f46] ring-2 ring-emerald-500/20 bg-emerald-50/40', 'badge' => 'bg-[#065f46] text-white'],
                        ['label' => 'Tomorrow', 'date' => \Carbon\Carbon::today()->addDay(), 'duty' => $tomorrowDuty, 'border' => 'border-indigo-200 bg-white', 'badge' => 'bg-indigo-50 text-indigo-800'],
                    ];
                @endphp

                @foreach($timelineCards as $card)
                    @php $d = $card['duty']; @endphp
                    <div class="p-4 rounded-2xl shadow-sm border {{ $card['border'] }} flex flex-col justify-between transition hover:shadow-md">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full {{ $card['badge'] }}">
                                    {{ $card['label'] }}
                                </span>
                                <div class="text-xs font-bold text-slate-700 mt-2">
                                    {{ $card['date']->format('d M Y (l)') }}
                                </div>
                            </div>
                            @if($d && $d->is_swapped)
                                <span class="bg-purple-100 text-purple-900 border border-purple-300 text-[10px] font-extrabold px-2 py-0.5 rounded-full">
                                    Swapped
                                </span>
                            @endif
                        </div>

                        <div class="my-3">
                            <div class="text-sm font-extrabold text-slate-900">
                                {{ $d ? $d->shift_name : 'Not Scheduled' }}
                            </div>
                            @if($d && $d->is_swapped)
                                <div class="text-[10px] font-bold text-purple-700 mt-0.5">&harr; {{ $d->swap_info }}</div>
                            @endif
                            @if($d && $d->has_overtime)
                                <div class="mt-1.5 inline-block bg-indigo-50 text-indigo-900 border border-indigo-200 px-2 py-0.5 rounded-lg text-[10px] font-extrabold">
                                    + OT: {{ $d->overtime_shift }} ({{ $d->overtime_hours }}h)
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-slate-200/80 pt-2.5 flex justify-between items-center text-[11px]">
                            <div class="text-slate-600 font-medium">
                                In: <strong class="text-slate-900">{{ $d && $d->in_time ? $d->in_time : '--:--' }}</strong>
                                <span class="mx-1 text-slate-300">|</span>
                                Out: <strong class="text-slate-900">{{ $d && $d->out_time ? $d->out_time : '--:--' }}</strong>
                            </div>
                            @if($d)
                                @if($d->attendance_status === 'Late')
                                    <span class="bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded font-extrabold text-[10px]">LATE ({{ $d->late_minutes }}m)</span>
                                @elseif($d->attendance_status === 'Present')
                                    <span class="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 rounded font-extrabold text-[10px]">PRESENT</span>
                                @elseif($d->attendance_status === 'Absent')
                                    <span class="bg-rose-600 text-white px-2 py-0.5 rounded font-extrabold text-[10px]">ABSENT</span>
                                @elseif($d->is_off_day)
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded font-extrabold text-[10px]">OFF DAY</span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-semibold text-[10px]">{{ $d->attendance_status }}</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Interactive Color-Coded Calendar with Shift Filter Pills -->
        @php
            $firstDay = mktime(0, 0, 0, $month, 1, $year);
            $daysInMonth = date('t', $firstDay);
            $startDayOfWeek = date('w', $firstDay);
            $monthTitle = date('F Y', $firstDay);

            function getDutyCellTheme($duty) {
                if (!$duty) return 'bg-slate-50/70 text-slate-400 border-slate-200';
                if ($duty->attendance_status === 'Absent') return 'bg-red-50 text-red-950 border-red-300';
                if ($duty->is_swapped) return 'bg-purple-50 text-purple-950 border-purple-300';
                if ($duty->is_off_day || str_contains(strtolower($duty->shift_name), 'off')) return 'bg-rose-50/80 text-rose-900 border-rose-200';
                if (str_contains(strtolower($duty->shift_name), 'morning')) return 'bg-emerald-50/90 text-emerald-950 border-emerald-300';
                if (str_contains(strtolower($duty->shift_name), 'evening')) return 'bg-amber-50/90 text-amber-950 border-amber-300';
                if (str_contains(strtolower($duty->shift_name), 'night')) return 'bg-indigo-50/90 text-indigo-950 border-indigo-300';
                return 'bg-teal-50 text-teal-950 border-teal-300';
            }

            function getDutyCategory($duty) {
                if (!$duty) return 'none';
                if ($duty->is_swapped) return 'swapped';
                if ($duty->is_off_day || str_contains(strtolower($duty->shift_name), 'off')) return 'off';
                if (str_contains(strtolower($duty->shift_name), 'morning')) return 'morning';
                if (str_contains(strtolower($duty->shift_name), 'evening')) return 'evening';
                if (str_contains(strtolower($duty->shift_name), 'night')) return 'night';
                return 'general';
            }
        @endphp

        <div class="bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-slate-200">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-5 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base md:text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-regular fa-calendar-days text-[#065f46]"></i>
                        {{ $monthTitle }} &mdash; Interactive Duty Roster Calendar ({{ $activeUser->name }})
                    </h3>
                    <p class="text-xs text-slate-500">Click any shift badge on the right to highlight specific shifts on the calendar.</p>
                </div>

                <!-- Interactive Filter Pills -->
                <div class="flex flex-wrap gap-1.5 text-[11px] font-bold">
                    <button type="button" onclick="filterCalendarCells('all')" class="px-2.5 py-1 rounded-lg bg-slate-800 text-white">All Days</button>
                    <button type="button" onclick="filterCalendarCells('morning')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-900 border border-emerald-300 hover:bg-emerald-100">Morning</button>
                    <button type="button" onclick="filterCalendarCells('evening')" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100">Evening</button>
                    <button type="button" onclick="filterCalendarCells('night')" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-900 border border-indigo-300 hover:bg-indigo-100">Night</button>
                    <button type="button" onclick="filterCalendarCells('swapped')" class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-900 border border-purple-300 hover:bg-purple-100">Swapped</button>
                    <button type="button" onclick="filterCalendarCells('off')" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-900 border border-rose-200 hover:bg-rose-100">Off Day</button>
                </div>
            </div>

            <div class="hidden md:grid grid-cols-7 gap-2.5 text-center font-extrabold text-xs uppercase text-slate-600 mb-2.5">
                <div class="bg-slate-100 py-2 rounded-xl">Sunday</div>
                <div class="bg-slate-100 py-2 rounded-xl">Monday</div>
                <div class="bg-slate-100 py-2 rounded-xl">Tuesday</div>
                <div class="bg-slate-100 py-2 rounded-xl">Wednesday</div>
                <div class="bg-slate-100 py-2 rounded-xl">Thursday</div>
                <div class="bg-slate-100 py-2 rounded-xl">Friday</div>
                <div class="bg-slate-100 py-2 rounded-xl">Saturday</div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-7 gap-2.5">
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="hidden md:block min-h-[7rem] bg-slate-50/40 rounded-xl border border-dashed border-slate-200"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $duty = $monthlyDuties->get($dateStr);
                        $isToday = ($dateStr === date('Y-m-d'));
                        $dayShort = date('D', strtotime($dateStr));
                        $cat = getDutyCategory($duty);
                    @endphp
                    <div class="cal-day-cell min-h-[7rem] p-2.5 rounded-xl border flex flex-col justify-between transition hover:shadow-md {{ getDutyCellTheme($duty) }} {{ $isToday ? 'ring-2 ring-[#065f46] shadow-md' : '' }}" data-cat="{{ $cat }}">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-extrabold {{ $isToday ? 'bg-[#065f46] text-white px-2 py-0.5 rounded-md' : '' }}">
                                {{ $day }} {{ date('M', strtotime($dateStr)) }}
                            </span>
                            <span class="text-[10px] font-bold uppercase opacity-70">{{ $dayShort }}</span>
                        </div>

                        <div class="my-1.5">
                            <div class="text-[11px] font-extrabold leading-snug">
                                {{ $duty ? $duty->shift_name : 'Unassigned' }}
                            </div>
                            @if($duty && $duty->is_swapped)
                                <div class="text-[9px] font-bold bg-purple-700 text-white px-1.5 py-0.5 rounded mt-1 inline-block">
                                    &harr; {{ $duty->swap_info }}
                                </div>
                            @endif
                            @if($duty && $duty->has_overtime)
                                <div class="text-[9px] font-bold bg-indigo-700 text-white px-1.5 py-0.5 rounded mt-1 inline-block">
                                    +OT: {{ $duty->overtime_shift }}
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between items-center text-[10px] font-semibold border-t border-black/10 pt-1">
                            <span>{{ $duty && $duty->in_time ? $duty->in_time : '--:--' }} - {{ $duty && $duty->out_time ? $duty->out_time : '--:--' }}</span>
                            @if($duty && $duty->attendance_status === 'Late')
                                <span class="bg-amber-500 text-slate-950 px-1.5 rounded text-[9px] font-extrabold">LATE</span>
                            @elseif($duty && $duty->attendance_status === 'Absent')
                                <span class="bg-rose-600 text-white px-1.5 rounded text-[9px] font-extrabold">ABSENT</span>
                            @elseif($duty && $duty->attendance_status === 'Present')
                                <span class="text-emerald-800 font-bold"><i class="fa-solid fa-check"></i></span>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Bottom Operational Hub: Today's Live Team Coverage & Shift Handover Logbook -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-5 bg-white p-5 rounded-3xl shadow-sm border border-slate-200 flex flex-col">
                <div class="border-b border-slate-100 pb-3 mb-3 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-users-viewfinder text-[#065f46]"></i>
                            Today's Front Desk Coverage ({{ date('d M Y') }})
                        </h3>
                        <p class="text-[11px] text-slate-500">Live view of colleagues scheduled across all shifts today.</p>
                    </div>
                    <span class="bg-emerald-50 border border-emerald-200 text-[#065f46] text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">
                        {{ $todayTeamRosters->count() }} Staff
                    </span>
                </div>

                <div class="space-y-2 max-h-80 overflow-y-auto flex-1 pr-1">
                    @forelse($todayTeamRosters as $td)
                    <div class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-lg bg-emerald-100 text-[#065f46] flex items-center justify-center font-bold text-xs">
                                {{ substr($td->user->name ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">{{ $td->user->name ?? 'Staff' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $td->user->designation ?? 'Front Desk' }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded font-bold text-[11px] {{ $td->is_off_day ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-900 border border-emerald-200' }}">
                                {{ explode('(', $td->shift_name)[0] }}
                            </span>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                In: <strong>{{ $td->in_time ?: '--:--' }}</strong> | Out: <strong>{{ $td->out_time ?: '--:--' }}</strong>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-xs text-slate-400 text-center py-8">No team roster generated for today yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-7 bg-white p-5 rounded-3xl shadow-sm border border-slate-200 flex flex-col">
                <div class="border-b border-slate-100 pb-3 mb-3 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-check text-amber-500"></i>
                            Front Desk Shift Handover & Communication Logbook
                        </h3>
                        <p class="text-[11px] text-slate-500">Leave important instructions or pending tasks for the next incoming shift.</p>
                    </div>
                </div>

                <form action="{{ route('handover.store') }}" method="POST" class="bg-slate-50 p-3 rounded-2xl border border-slate-200 mb-4 flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs">
                    @csrf
                    <select name="priority" class="border border-slate-300 p-2 rounded-xl font-bold bg-white">
                        <option value="normal">Normal Note</option>
                        <option value="urgent">Urgent Alert</option>
                    </select>
                    <input type="text" name="message" placeholder="Write handover note for incoming shift (e.g. Pending corporate billing / VIP appointment)..." class="flex-1 border border-slate-300 p-2 rounded-xl bg-white w-full" required>
                    <button type="submit" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-bold px-4 py-2 rounded-xl shrink-0 shadow-sm">
                        Post Note
                    </button>
                </form>

                <div class="space-y-2 max-h-60 overflow-y-auto flex-1 pr-1">
                    @forelse($handoverNotes as $note)
                    <div class="p-3 rounded-xl border {{ $note->is_resolved ? 'bg-slate-50 border-slate-200 opacity-60' : ($note->priority === 'urgent' ? 'bg-rose-50/60 border-rose-200' : 'bg-amber-50/40 border-amber-200') }} flex justify-between items-start gap-3 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900">{{ $note->user->name ?? 'Staff' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $note->created_at->diffForHumans() }}</span>
                                @if($note->priority === 'urgent' && !$note->is_resolved)
                                    <span class="bg-rose-600 text-white text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded">Urgent</span>
                                @endif
                                @if($note->is_resolved)
                                    <span class="bg-emerald-100 text-emerald-800 text-[9px] font-bold uppercase px-1.5 py-0.5 rounded">Resolved</span>
                                @endif
                            </div>
                            <p class="text-slate-700 mt-1 {{ $note->is_resolved ? 'line-through' : 'font-medium' }}">{{ $note->message }}</p>
                        </div>
                        <form action="{{ route('handover.resolve', $note->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold px-2.5 py-1 rounded-lg border {{ $note->is_resolved ? 'bg-white text-slate-600' : 'bg-emerald-600 text-white border-emerald-600' }}">
                                {{ $note->is_resolved ? 'Reopen' : 'Mark Done' }}
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="text-xs text-slate-400 text-center py-6">
                        No shift handover notes posted yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script>
        function filterCalendarCells(cat) {
            document.querySelectorAll('.cal-day-cell').forEach(function(cell) {
                if (cat === 'all' || cell.getAttribute('data-cat') === cat) {
                    cell.style.opacity = '1';
                    cell.style.transform = 'scale(1)';
                } else {
                    cell.style.opacity = '0.25';
                    cell.style.transform = 'scale(0.97)';
                }
            });
        }
    </script>
</body>
</html>
