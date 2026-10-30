<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>A4 HR Attendance Sheet - {{ $reportUser->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            @page { size: A4 portrait; margin: 6mm; }
            body { background: #fff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            .a4 { border: none !important; box-shadow: none !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
            table { font-size: 8.5px !important; }
            th, td { padding: 2px 4px !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="no-print">
        @include('partials.nav')
    </div>
    <div class="no-print max-w-5xl mx-auto px-4 pt-4">
        <div class="bg-white p-3.5 rounded-xl border flex flex-wrap justify-between items-center gap-2 text-xs">
            <form method="GET" action="{{ route('report.print') }}" class="flex flex-wrap items-center gap-2">
                <select name="user_id" class="border rounded p-1.5 font-semibold bg-slate-50">
                    @foreach($team as $m) <option value="{{ $m->id }}" {{ $reportUser->id == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->employee_id }})</option> @endforeach
                </select>
                <select name="mode" onchange="this.form.submit()" class="border rounded p-1.5 font-semibold bg-emerald-50">
                    <option value="monthly" {{ $filterMode === 'monthly' ? 'selected' : '' }}>Monthly View</option>
                    <option value="custom" {{ $filterMode === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    <option value="daily" {{ $filterMode === 'daily' ? 'selected' : '' }}>Single Day</option>
                </select>
                @if($filterMode === 'monthly')
                    <select name="month" class="border rounded p-1.5">@for($m=1;$m<=12;$m++) <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option> @endfor</select>
                    <select name="year" class="border rounded p-1.5">@for($y=2025;$y<=2028;$y++) <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option> @endfor</select>
                @elseif($filterMode === 'custom')
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="border rounded p-1">
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-t')) }}" class="border rounded p-1">
                @elseif($filterMode === 'daily')
                    <input type="date" name="single_date" value="{{ request('single_date', date('Y-m-d')) }}" class="border rounded p-1">
                @endif
                <button type="submit" class="bg-[#065f46] text-white font-bold px-3 py-1.5 rounded">Apply</button>
            </form>
            <div class="flex gap-2">
                <a href="{{ route('report.csv', ['user_id' => $reportUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-slate-100 border font-bold px-3 py-1.5 rounded">Export CSV</a>
                <button onclick="window.print()" class="bg-slate-900 text-white font-bold px-4 py-1.5 rounded">Print 1-Page A4</button>
            </div>
        </div>
    </div>

    <div class="a4 max-w-5xl mx-auto my-4 bg-white p-6 rounded-xl border shadow-sm">
        <div class="border-b-2 border-[#064e3b] pb-2 mb-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                @if(!empty($setting->logo_url)) <img src="{{ $setting->logo_url }}" class="h-11 w-11 object-contain"> @endif
                <div>
                    <h1 class="text-xl font-extrabold text-[#064e3b] uppercase">{{ $setting->hospital_name }}</h1>
                    <p class="text-[11px] text-slate-600 font-medium">{{ $setting->hospital_address }}</p>
                    <p class="text-xs font-bold text-slate-800 uppercase mt-0.5">{{ $setting->department_name }} &mdash; Official Duty Roster & Attendance Sheet</p>
                </div>
            </div>
            <div class="text-right text-xs bg-slate-50 border p-2 rounded">
                <div class="font-bold">{{ $reportUser->name }} ({{ $reportUser->employee_id }})</div>
                <div>{{ $reportUser->designation }} | Period: {{ $periodLabel }}</div>
            </div>
        </div>

        <div class="grid grid-cols-6 gap-2 mb-3 text-center text-[10px]">
            <div class="bg-slate-50 border p-1 rounded font-bold">Regular Duty: {{ $summary['regular_duty'] }}</div>
            <div class="bg-emerald-50 border border-emerald-300 p-1 rounded font-bold text-emerald-900">Present: {{ $summary['present_count'] }}</div>
            <div class="bg-rose-50 border border-rose-300 p-1 rounded font-bold text-rose-900">Off Days: {{ $summary['off_days'] }}</div>
            <div class="bg-amber-50 border border-amber-300 p-1 rounded font-bold text-amber-900">Late: {{ $summary['late_count'] }}</div>
            <div class="bg-indigo-50 border border-indigo-300 p-1 rounded font-bold text-indigo-900">Overtime: {{ $summary['ot_count'] }} ({{ $summary['ot_hours'] }}h)</div>
            <div class="bg-purple-50 border border-purple-300 p-1 rounded font-bold text-purple-900">Swapped: {{ $summary['swapped_count'] }}</div>
        </div>

        <table class="w-full border-collapse border text-[9.5px]">
            <thead>
                <tr class="bg-[#064e3b] text-white">
                    <th class="border p-1 text-left">Date</th><th class="border p-1 text-left">Day</th><th class="border p-1 text-left">Regular Shift</th>
                    <th class="border p-1 text-center">In-Time</th><th class="border p-1 text-center">Out-Time</th><th class="border p-1 text-left">Overtime Duty</th>
                    <th class="border p-1 text-center">Status</th><th class="border p-1 text-left">Swap / Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                @php
                    $bg = $r->attendance_status === 'Late' ? 'bg-amber-50' : ($r->attendance_status === 'Absent' ? 'bg-red-50' : ($r->has_overtime ? 'bg-indigo-50/60' : ($r->is_swapped ? 'bg-purple-50/60' : ($r->is_off_day ? 'bg-rose-50/40' : ''))));
                @endphp
                <tr class="border-b {{ $bg }}">
                    <td class="border p-1 font-semibold">{{ $r->duty_date }}</td><td class="border p-1">{{ $r->day_name }}</td>
                    <td class="border p-1 font-semibold">{{ $r->shift_name }}</td><td class="border p-1 text-center">{{ $r->in_time ?: '-' }}</td>
                    <td class="border p-1 text-center">{{ $r->out_time ?: '-' }}</td>
                    <td class="border p-1 font-bold text-indigo-900">{{ $r->has_overtime ? $r->overtime_shift . ' (' . $r->overtime_hours . 'h)' : '-' }}</td>
                    <td class="border p-1 text-center font-bold">{{ $r->attendance_status }}</td>
                    <td class="border p-1">{{ $r->swap_info }} {{ $r->remarks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-10 grid grid-cols-4 gap-4 text-center text-xs">
            <div><div class="border-t border-slate-800 pt-1 font-bold">{{ $setting->sig1_name }}</div><div class="text-[10px] text-slate-500">{{ $setting->sig1_title }}</div></div>
            <div><div class="border-t border-slate-800 pt-1 font-bold">{{ $setting->sig2_name }}</div><div class="text-[10px] text-slate-500">{{ $setting->sig2_title }}</div></div>
            <div><div class="border-t border-slate-800 pt-1 font-bold">{{ $setting->sig3_name }}</div><div class="text-[10px] text-slate-500">{{ $setting->sig3_title }}</div></div>
            <div><div class="border-t border-slate-800 pt-1 font-bold">{{ $setting->sig4_name }}</div><div class="text-[10px] text-slate-500">{{ $setting->sig4_title }}</div></div>
        </div>
    </div>
</body>
</html>
