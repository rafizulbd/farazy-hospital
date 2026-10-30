<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | {{ $setting->hospital_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">
    @include('partials.nav')

    <main class="max-w-7xl mx-auto w-full px-4 py-6 space-y-6 flex-1">
        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-300 text-emerald-900 font-semibold rounded-xl text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}
            </div>
        @endif

        @if($pendingSwapsCount > 0)
            <div class="p-3.5 bg-purple-50 border border-purple-300 text-purple-950 rounded-xl flex justify-between items-center text-xs font-bold">
                <span><i class="fa-solid fa-bell text-purple-600 mr-2"></i> You have <strong>{{ $pendingSwapsCount }} Shift Swap Request(s)</strong> awaiting your response.</span>
                <a href="{{ route('rosters.index') }}#swap-section" class="bg-purple-700 text-white px-3 py-1 rounded">Review</a>
            </div>
        @endif

        @if(Auth::user()->role === 'admin' && ($adminPendingSwaps > 0 || $adminPendingUpdates > 0))
            <div class="p-3.5 bg-amber-50 border border-amber-300 text-amber-950 rounded-xl flex flex-wrap justify-between items-center gap-2 text-xs font-bold">
                <span><i class="fa-solid fa-user-shield text-amber-600 mr-1.5"></i> In-Charge Action Required: {{ $adminPendingSwaps }} Swap(s) & {{ $adminPendingUpdates }} Time Correction(s) waiting.</span>
                <a href="{{ route('admin.panel') }}" class="bg-amber-600 text-white px-3 py-1 rounded">Open Approvals</a>
            </div>
        @endif

        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-[#064e3b] text-white flex items-center justify-center font-bold">
                    {{ substr($activeUser->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-slate-900">{{ $activeUser->name }}</h2>
                        <span class="bg-slate-100 border text-slate-700 text-[11px] font-semibold px-2 py-0.5 rounded">{{ $activeUser->employee_id }}</span>
                        @if($setting->is_verified)
                            <span class="bg-emerald-50 border border-emerald-300 text-emerald-800 text-[11px] font-bold px-2 py-0.5 rounded">Verified Schedule</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500">{{ $activeUser->designation }} &bull; {{ $setting->validity_text }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2 text-xs">
                <select name="user_id" onchange="this.form.submit()" class="border rounded-lg px-2.5 py-1.5 font-semibold bg-slate-50">
                    @foreach($team as $member)
                        <option value="{{ $member->id }}" {{ $activeUser->id == $member->id ? 'selected' : '' }}>{{ $member->name }} ({{ $member->employee_id }})</option>
                    @endforeach
                </select>
                <select name="month" onchange="this.form.submit()" class="border rounded-lg px-2.5 py-1.5 font-semibold bg-slate-50">
                    @for($m = 1; $m <= 12; $m++) <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option> @endfor
                </select>
                <select name="year" onchange="this.form.submit()" class="border rounded-lg px-2.5 py-1.5 font-semibold bg-slate-50">
                    @for($y = 2025; $y <= 2028; $y++) <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option> @endfor
                </select>
                <a href="{{ route('report.print', ['user_id' => $activeUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-slate-800 text-white px-3 py-1.5 rounded-lg font-semibold">Print A4</a>
            </form>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-center">
            <div class="bg-white p-3 rounded-xl border shadow-sm"><span class="text-[11px] text-slate-500 block">Regular Duty</span><span class="text-lg font-bold">{{ $kpi['regular_days'] }}</span></div>
            <div class="bg-white p-3 rounded-xl border shadow-sm"><span class="text-[11px] text-emerald-700 block">Present</span><span class="text-lg font-bold text-emerald-700">{{ $kpi['present_days'] }}</span></div>
            <div class="bg-white p-3 rounded-xl border shadow-sm"><span class="text-[11px] text-rose-700 block">Off Days</span><span class="text-lg font-bold text-rose-700">{{ $kpi['off_days'] }}</span></div>
            <div class="bg-white p-3 rounded-xl border shadow-sm"><span class="text-[11px] text-amber-700 block">Late Count</span><span class="text-lg font-bold text-amber-700">{{ $kpi['late_days'] }}</span></div>
            <div class="bg-white p-3 rounded-xl border shadow-sm"><span class="text-[11px] text-indigo-700 block">Overtime</span><span class="text-lg font-bold text-indigo-700">{{ $kpi['ot_hours'] }}h</span></div>
            <div class="bg-white p-3 rounded-xl border shadow-sm"><span class="text-[11px] text-purple-700 block">Swapped</span><span class="text-lg font-bold text-purple-700">{{ $kpi['swapped_days'] }}</span></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['2 Days Ago', \Carbon\Carbon::today()->subDays(2), $twoDaysAgoDuty, 'bg-white border-slate-200'],
                    ['Yesterday', \Carbon\Carbon::today()->subDay(), $yesterdayDuty, 'bg-white border-slate-200'],
                    ['Today (Active)', \Carbon\Carbon::today(), $todayDuty, 'bg-emerald-50/50 border-[#065f46] ring-2 ring-emerald-500/20'],
                    ['Tomorrow', \Carbon\Carbon::today()->addDay(), $tomorrowDuty, 'bg-white border-slate-200'],
                ];
            @endphp
            @foreach($cards as $idx => [$label, $date, $duty, $style])
            <div class="p-4 rounded-xl border shadow-sm {{ $style }} flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $idx === 2 ? 'bg-[#065f46] text-white' : 'bg-slate-100' }}">{{ $label }}</span>
                    <span class="text-xs font-bold text-slate-700">{{ $date->format('d M, D') }}</span>
                </div>
                <div class="my-2.5">
                    <div class="text-sm font-extrabold text-slate-900">{{ $duty ? $duty->shift_name : 'No Shift' }}</div>
                    @if($duty && $duty->has_overtime)
                        <span class="text-[10px] font-bold bg-indigo-50 text-indigo-800 border px-1.5 py-0.5 rounded mt-1 inline-block">+OT: {{ $duty->overtime_shift }} ({{ $duty->overtime_hours }}h)</span>
                    @endif
                </div>
                <div class="border-t pt-2 flex justify-between items-center text-[11px] text-slate-600">
                    <span>In: <strong>{{ $duty && $duty->in_time ? $duty->in_time : '--:--' }}</strong></span>
                    <span>Out: <strong>{{ $duty && $duty->out_time ? $duty->out_time : '--:--' }}</strong></span>
                    @if($duty && $duty->attendance_status === 'Late') <span class="bg-amber-100 text-amber-900 px-1.5 py-0.5 rounded font-bold text-[9px]">LATE</span>
                    @elseif($duty && $duty->attendance_status === 'Present') <span class="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-bold text-[9px]">PRESENT</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($todayDuty && ($activeUser->id === Auth::id() || Auth::user()->role === 'admin'))
        <div class="bg-white p-4 rounded-xl border border-l-4 border-l-[#065f46] flex flex-wrap justify-between items-center gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900"><i class="fa-solid fa-fingerprint text-[#065f46] mr-1"></i> Today's Time Entry ({{ date('d M Y') }} &mdash; {{ $todayDuty->shift_name }})</h3>
                <p class="text-xs text-slate-500">Punch In/Out saves and locks your entry automatically.</p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$todayDuty->in_locked)
                <form action="{{ route('attendance.punch', $todayDuty->id) }}" method="POST" class="flex gap-1" onsubmit="return confirm('Confirm In-Time? Once saved, it will be locked.');">
                    @csrf <input type="hidden" name="punch_type" value="in_time">
                    <input type="time" name="in_time" value="{{ date('H:i') }}" class="border rounded p-1 text-xs font-semibold" required>
                    <button class="bg-[#065f46] text-white px-2.5 py-1 rounded text-xs font-semibold">Save In</button>
                </form>
                @else
                <span class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-2.5 py-1 rounded text-xs font-semibold">In: {{ $todayDuty->in_time }}</span>
                @endif

                @if(!$todayDuty->out_locked)
                <form action="{{ route('attendance.punch', $todayDuty->id) }}" method="POST" class="flex gap-1" onsubmit="return confirm('Confirm Out-Time? Once saved, it will be locked.');">
                    @csrf <input type="hidden" name="punch_type" value="out_time">
                    <input type="time" name="out_time" value="{{ date('H:i') }}" class="border rounded p-1 text-xs font-semibold" required>
                    <button class="bg-slate-800 text-white px-2.5 py-1 rounded text-xs font-semibold">Save Out</button>
                </form>
                @else
                <span class="bg-slate-100 border text-slate-800 px-2.5 py-1 rounded text-xs font-semibold">Out: {{ $todayDuty->out_time }}</span>
                @endif
            </div>
        </div>
        @endif

        @php
            $firstDay = mktime(0, 0, 0, $month, 1, $year);
            $daysInMonth = date('t', $firstDay);
            $startDayOfWeek = date('w', $firstDay);
            function calColor($d) {
                if (!$d) return 'bg-slate-50 text-slate-400';
                if ($d->attendance_status === 'Absent') return 'bg-red-50 text-red-950 border-red-300';
                if ($d->is_swapped) return 'bg-purple-50 text-purple-950 border-purple-300';
                if ($d->is_off_day || str_contains(strtolower($d->shift_name), 'off')) return 'bg-rose-50 text-rose-900 border-rose-200';
                if (str_contains(strtolower($d->shift_name), 'morning')) return 'bg-emerald-50 text-emerald-950 border-emerald-300';
                if (str_contains(strtolower($d->shift_name), 'evening')) return 'bg-amber-50 text-amber-950 border-amber-300';
                if (str_contains(strtolower($d->shift_name), 'night')) return 'bg-indigo-50 text-indigo-950 border-indigo-300';
                return 'bg-teal-50 text-teal-950 border-teal-300';
            }
        @endphp
        <div class="bg-white p-5 rounded-xl border">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
                <h3 class="text-base font-bold text-slate-900">{{ date('F Y', $firstDay) }} Interactive Duty Calendar</h3>
                <div class="flex flex-wrap gap-1.5 text-[10px] font-bold">
                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-900 border border-emerald-300">Morning</span>
                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-900 border border-amber-300">Evening</span>
                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-900 border border-indigo-300">Night</span>
                    <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-900 border border-purple-300">Swapped</span>
                    <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-900 border border-rose-200">Off Day</span>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $duty = $monthlyDuties->get($dateStr);
                        $isToday = ($dateStr === date('Y-m-d'));
                    @endphp
                    <div class="min-h-[5.8rem] p-2 rounded-lg border flex flex-col justify-between {{ calColor($duty) }} {{ $isToday ? 'ring-2 ring-[#065f46]' : '' }}">
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="{{ $isToday ? 'bg-[#065f46] text-white px-1.5 py-0.5 rounded' : '' }}">{{ $day }} {{ date('M', strtotime($dateStr)) }}</span>
                            <span class="text-[10px] opacity-70">{{ date('D', strtotime($dateStr)) }}</span>
                        </div>
                        <div class="text-[11px] font-bold leading-tight my-1">
                            {{ $duty ? $duty->shift_name : '-' }}
                            @if($duty && $duty->is_swapped) <span class="block text-[9px] text-purple-800">{{ $duty->swap_info }}</span> @endif
                        </div>
                        <div class="text-[9.5px] opacity-80 border-t pt-1 flex justify-between">
                            <span>{{ $duty && $duty->in_time ? $duty->in_time : '--' }}-{{ $duty && $duty->out_time ? $duty->out_time : '--' }}</span>
                            @if($duty && $duty->attendance_status === 'Late') <span class="font-bold text-amber-700">LATE</span> @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </main>
</body>
</html>
