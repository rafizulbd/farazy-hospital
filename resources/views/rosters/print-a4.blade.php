<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A4 HR Roster & Attendance Report - {{ $reportUser->name }}</title>
    @if(!empty($setting->favicon_url))
        <link rel="icon" href="{{ $setting->favicon_url }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            @page { size: A4 portrait; margin: 6mm; }
            body { background: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            .a4-sheet { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            table { font-size: 8.8px !important; }
            th, td { padding: 2px 4px !important; line-height: 1.15 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">
    <div class="no-print">
        @include('partials.nav')
    </div>

    <!-- Top Non-Printable Filter & Action Bar -->
    <div class="no-print max-w-5xl mx-auto px-4 pt-5">
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-wrap justify-between items-center gap-3">
            <form method="GET" action="{{ route('report.print') }}" class="flex flex-wrap items-center gap-2 text-xs">
                <select name="user_id" class="border border-slate-300 rounded-xl px-3 py-2 font-bold bg-slate-50">
                    @foreach($team as $m)
                        <option value="{{ $m->id }}" {{ $reportUser->id == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->employee_id }})</option>
                    @endforeach
                </select>

                <select name="mode" onchange="this.form.submit()" class="border border-emerald-300 rounded-xl px-3 py-2 font-bold bg-emerald-50 text-emerald-950">
                    <option value="monthly" {{ $filterMode === 'monthly' ? 'selected' : '' }}>Monthly Sheet</option>
                    <option value="custom" {{ $filterMode === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    <option value="daily" {{ $filterMode === 'daily' ? 'selected' : '' }}>Single Day</option>
                </select>

                @if($filterMode === 'monthly')
                    <select name="month" class="border border-slate-300 rounded-xl px-3 py-2 font-bold">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                    <select name="year" class="border border-slate-300 rounded-xl px-3 py-2 font-bold">
                        @for($y = 2025; $y <= 2028; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                @elseif($filterMode === 'custom')
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="border rounded-xl px-2.5 py-1.5 font-bold">
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-t')) }}" class="border rounded-xl px-2.5 py-1.5 font-bold">
                @elseif($filterMode === 'daily')
                    <input type="date" name="single_date" value="{{ request('single_date', date('Y-m-d')) }}" class="border rounded-xl px-2.5 py-1.5 font-bold">
                @endif

                <button type="submit" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-bold px-4 py-2 rounded-xl shadow-sm">
                    Filter Preview
                </button>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('report.csv', ['user_id' => $reportUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-bold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-file-csv text-emerald-700"></i> Download CSV
                </a>
                <button onclick="window.print()" class="bg-slate-900 hover:bg-black text-white font-extrabold px-5 py-2 rounded-xl text-xs shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Print 1-Page A4 Sheet
                </button>
            </div>
        </div>
    </div>

    <!-- Printable 1-Page A4 Sheet Container -->
    <div class="a4-sheet max-w-5xl mx-auto my-5 bg-white p-7 rounded-2xl shadow-lg border border-slate-200">
        <!-- Official Hospital Header -->
        <div class="border-b-2 border-[#064e3b] pb-3 mb-3 flex justify-between items-center gap-4">
            <div class="flex items-center gap-3.5">
                @if(!empty($setting->logo_url))
                    <img src="{{ $setting->logo_url }}" alt="Logo" class="h-14 w-14 object-contain">
                @else
                    <div class="h-12 w-12 rounded-xl bg-[#064e3b] text-white flex items-center justify-center font-bold text-xl">
                        <i class="fa-regular fa-hospital"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-extrabold text-[#064e3b] uppercase tracking-tight leading-none">{{ $setting->hospital_name }}</h1>
                    <p class="text-[11px] text-slate-600 font-medium mt-0.5">{{ $setting->hospital_address }}</p>
                    @if(!empty($setting->hospital_phone) || !empty($setting->hospital_email))
                        <p class="text-[10px] text-slate-500">
                            {{ $setting->hospital_phone ? 'Phone: ' . $setting->hospital_phone : '' }}
                            {{ $setting->hospital_email ? ' | Email: ' . $setting->hospital_email : '' }}
                        </p>
                    @endif
                    <p class="text-xs font-extrabold text-slate-900 uppercase mt-0.5">{{ $setting->department_name }} &mdash; Official Duty Roster & Attendance Statement</p>
                </div>
            </div>

            <div class="text-right text-xs shrink-0">
                <div class="bg-slate-50 border border-slate-300 px-3.5 py-2 rounded-xl">
                    <div class="font-extrabold text-slate-900 text-sm">{{ $reportUser->name }}</div>
                    <div class="font-semibold text-slate-600">{{ $reportUser->designation }} | ID: {{ $reportUser->employee_id }}</div>
                    <div class="text-[11px] font-extrabold text-[#065f46] mt-0.5">Period: {{ $periodLabel }}</div>
                </div>
            </div>
        </div>

        <!-- 6 Summary KPI Boxes -->
        <div class="grid grid-cols-6 gap-2 mb-3 text-center">
            <div class="bg-emerald-50 border border-emerald-200 p-1.5 rounded-lg">
                <div class="text-[9px] font-bold uppercase text-emerald-800">Regular Duty</div>
                <div class="text-sm font-extrabold text-emerald-950">{{ $summary['regular_duty'] }} <span class="text-[9px] font-normal">Days</span></div>
            </div>
            <div class="bg-teal-50 border border-teal-200 p-1.5 rounded-lg">
                <div class="text-[9px] font-bold uppercase text-teal-800">Present Logged</div>
                <div class="text-sm font-extrabold text-teal-950">{{ $summary['present_count'] }} <span class="text-[9px] font-normal">Days</span></div>
            </div>
            <div class="bg-rose-50 border border-rose-200 p-1.5 rounded-lg">
                <div class="text-[9px] font-bold uppercase text-rose-800">Weekly Off</div>
                <div class="text-sm font-extrabold text-rose-900">{{ $summary['off_days'] }} <span class="text-[9px] font-normal">Days</span></div>
            </div>
            <div class="bg-amber-50 border border-amber-300 p-1.5 rounded-lg">
                <div class="text-[9px] font-bold uppercase text-amber-900">Late Count</div>
                <div class="text-sm font-extrabold text-amber-900">{{ $summary['late_count'] }} <span class="text-[9px] font-normal">Days</span></div>
            </div>
            <div class="bg-indigo-50 border border-indigo-200 p-1.5 rounded-lg">
                <div class="text-[9px] font-bold uppercase text-indigo-900">Overtime</div>
                <div class="text-sm font-extrabold text-indigo-950">{{ $summary['ot_count'] }} <span class="text-[9px] font-normal">({{ $summary['ot_hours'] }}h)</span></div>
            </div>
            <div class="bg-purple-50 border border-purple-200 p-1.5 rounded-lg">
                <div class="text-[9px] font-bold uppercase text-purple-900">Swapped Duty</div>
                <div class="text-sm font-extrabold text-purple-950">{{ $summary['swapped_count'] }} <span class="text-[9px] font-normal">Days</span></div>
            </div>
        </div>

        <!-- Full Month Attendance Table -->
        <table class="w-full border-collapse border border-slate-300 text-[10px]">
            <thead>
                <tr class="bg-[#064e3b] text-white uppercase">
                    <th class="border border-slate-300 p-1 text-left">Date</th>
                    <th class="border border-slate-300 p-1 text-left">Day</th>
                    <th class="border border-slate-300 p-1 text-left">Regular Assigned Shift</th>
                    <th class="border border-slate-300 p-1 text-center">In-Time</th>
                    <th class="border border-slate-300 p-1 text-center">Out-Time</th>
                    <th class="border border-slate-300 p-1 text-left">Overtime Shift (Hours)</th>
                    <th class="border border-slate-300 p-1 text-center">Status</th>
                    <th class="border border-slate-300 p-1 text-left">Swap / Official Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                @php
                    $trClass = '';
                    if ($r->attendance_status === 'Late') $trClass = 'bg-amber-50/90';
                    elseif ($r->attendance_status === 'Absent') $trClass = 'bg-red-50/90';
                    elseif ($r->has_overtime) $trClass = 'bg-indigo-50/60';
                    elseif ($r->is_swapped) $trClass = 'bg-purple-50/60';
                    elseif ($r->is_off_day) $trClass = 'bg-slate-50';
                @endphp
                <tr class="{{ $trClass }}">
                    <td class="border border-slate-300 p-1 font-bold">{{ \Carbon\Carbon::parse($r->duty_date)->format('d M Y') }}</td>
                    <td class="border border-slate-300 p-1">{{ $r->day_name }}</td>
                    <td class="border border-slate-300 p-1 font-semibold text-slate-900">{{ $r->shift_name }}</td>
                    <td class="border border-slate-300 p-1 text-center font-bold">{{ $r->in_time ?: '-' }}</td>
                    <td class="border border-slate-300 p-1 text-center font-bold">{{ $r->out_time ?: '-' }}</td>
                    <td class="border border-slate-300 p-1">
                        @if($r->has_overtime)
                            <span class="font-extrabold text-indigo-900">OT: {{ $r->overtime_shift }} ({{ $r->overtime_hours }}h)</span>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="border border-slate-300 p-1 text-center font-extrabold">
                        @if($r->attendance_status === 'Late')
                            <span class="text-amber-800">LATE ({{ $r->late_minutes }}m)</span>
                        @elseif($r->attendance_status === 'Absent')
                            <span class="text-rose-700">ABSENT</span>
                        @elseif($r->attendance_status === 'Present')
                            <span class="text-emerald-800">PRESENT</span>
                        @elseif($r->is_off_day)
                            <span class="text-rose-700">OFF DAY</span>
                        @else
                            <span class="text-slate-500">{{ $r->attendance_status }}</span>
                        @endif
                    </td>
                    <td class="border border-slate-300 p-1 text-[9.5px]">
                        @if($r->is_swapped)
                            <strong class="text-purple-800">{{ $r->swap_info }}</strong>
                        @endif
                        {{ $r->remarks }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- 4 Customizable HR Signatory Blocks -->
        <div class="mt-10 pt-4 grid grid-cols-4 gap-6 text-center text-xs">
            <div>
                <div class="border-t-2 border-slate-800 pt-1.5 font-extrabold text-slate-900">{{ $setting->sig1_name }}</div>
                <div class="text-[10px] font-medium text-slate-600">{{ $setting->sig1_title }}</div>
            </div>
            <div>
                <div class="border-t-2 border-slate-800 pt-1.5 font-extrabold text-slate-900">{{ $setting->sig2_name }}</div>
                <div class="text-[10px] font-medium text-slate-600">{{ $setting->sig2_title }}</div>
            </div>
            <div>
                <div class="border-t-2 border-slate-800 pt-1.5 font-extrabold text-slate-900">{{ $setting->sig3_name }}</div>
                <div class="text-[10px] font-medium text-slate-600">{{ $setting->sig3_title }}</div>
            </div>
            <div>
                <div class="border-t-2 border-slate-800 pt-1.5 font-extrabold text-slate-900">{{ $setting->sig4_name }}</div>
                <div class="text-[10px] font-medium text-slate-600">{{ $setting->sig4_title }}</div>
            </div>
        </div>

        <div class="mt-3 text-[9px] text-slate-400 border-t pt-1.5 flex justify-between">
            <span>{{ $setting->hospital_name }} &mdash; {{ $setting->department_name }}</span>
            <span>Printed: {{ date('d M Y, h:i A') }} | Status: {{ $setting->is_verified ? 'Verified by ' . $setting->verified_by : 'Draft / Review' }}</span>
        </div>
    </div>
</body>
</html>
