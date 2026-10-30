<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster, Swap & Attendance | {{ $setting->hospital_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">
    @include('partials.nav')

    <main class="max-w-7xl mx-auto w-full px-4 py-6 space-y-6 flex-1">
        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-lg text-xs font-semibold">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-300 text-rose-900 rounded-lg text-xs font-semibold">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white p-4 rounded-xl border flex flex-wrap justify-between items-center gap-3">
            <div>
                <h1 class="text-base font-bold text-slate-900">Roster Scheduling, Shift Swap & Attendance ({{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }})</h1>
                <p class="text-xs text-slate-500">6 Days Duty + 1 Day Off Pattern &bull; 2-Stage Mutual Swap &bull; Auto-Locked In/Out</p>
            </div>
            <form method="GET" action="{{ route('rosters.index') }}" class="flex items-center gap-2 text-xs">
                @if(Auth::user()->role === 'admin')
                <select name="user_id" onchange="this.form.submit()" class="border rounded p-1.5 font-bold bg-emerald-50">
                    @foreach($team as $m) <option value="{{ $m->id }}" {{ $targetUser->id == $m->id ? 'selected' : '' }}>Staff: {{ $m->name }}</option> @endforeach
                </select>
                @endif
                <select name="month" onchange="this.form.submit()" class="border rounded p-1.5">
                    @for($m = 1; $m <= 12; $m++) <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option> @endfor
                </select>
                <select name="year" onchange="this.form.submit()" class="border rounded p-1.5">
                    @for($y = 2025; $y <= 2028; $y++) <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option> @endfor
                </select>
            </form>
        </div>

        @if(Auth::user()->role === 'admin')
        <div class="bg-[#064e3b] text-white p-3.5 rounded-xl border flex flex-wrap justify-between items-center gap-2 text-xs">
            <span class="font-bold text-amber-300"><i class="fa-solid fa-sliders mr-1"></i> In-Charge Roster Phase & Merge Controller</span>
            <form action="{{ route('rosters.merge') }}" method="POST" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="year" value="{{ $year }}">
                <select name="next_phase" class="bg-[#022c22] border border-emerald-600 text-white p-1.5 rounded">
                    <option value="draft_open" {{ $setting->roster_phase === 'draft_open' ? 'selected' : '' }}>Phase 1: Draft Open (29th)</option>
                    <option value="merge_review" {{ $setting->roster_phase === 'merge_review' ? 'selected' : '' }}>Phase 2: Merged & Swap Open (30th)</option>
                    <option value="final_verified" {{ $setting->roster_phase === 'final_verified' ? 'selected' : '' }}>Phase 3: Final Verified (31st)</option>
                    <option value="closed" {{ $setting->roster_phase === 'closed' ? 'selected' : '' }}>Portal Closed</option>
                </select>
                <input type="text" name="verified_by" value="{{ $setting->verified_by }}" placeholder="Verified By Authority" class="bg-[#022c22] border border-emerald-600 text-white p-1.5 rounded">
                <button type="submit" class="bg-amber-400 text-slate-950 font-bold px-3 py-1.5 rounded">Merge & Apply Phase</button>
            </form>
        </div>
        @endif

        <div class="bg-white p-5 rounded-xl border">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-3 border-b pb-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Step 1: 7-Day Weekly Draft Roster ({{ $targetUser->name }})</h2>
                    <p class="text-xs text-slate-500">Select 6 Days Duty and exactly 1 Day Off. Saving locks your draft and unlocks the team overview below.</p>
                </div>
                <div class="flex gap-2 text-xs font-bold">
                    <span class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-2.5 py-0.5 rounded">Submitted: {{ count($submittedUsers) }}</span>
                    <span class="bg-amber-50 border border-amber-200 text-amber-800 px-2.5 py-0.5 rounded">Pending: {{ count($pendingUsers) }}</span>
                </div>
            </div>

            @php
                $canEdit = ($setting->roster_phase === 'draft_open' && (!$myDraft || !$myDraft->is_submitted)) || Auth::user()->role === 'admin';
                $days = ['sun_shift'=>'Sunday','mon_shift'=>'Monday','tue_shift'=>'Tuesday','wed_shift'=>'Wednesday','thu_shift'=>'Thursday','fri_shift'=>'Friday','sat_shift'=>'Saturday'];
            @endphp

            @if($myDraft && $myDraft->is_submitted && Auth::user()->role !== 'admin')
                <div class="p-2.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded text-xs font-semibold mb-3">
                    <i class="fa-solid fa-lock mr-1"></i> Your 7-Day Draft Roster is submitted and locked.
                </div>
            @endif

            <form action="{{ route('rosters.draft.save') }}" method="POST" onsubmit="return confirm('Confirm Draft Roster submission? Make sure you selected 6 Days Duty and 1 Off Day. Once saved, it will be locked.');">
                @csrf
                <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                <input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="year" value="{{ $year }}">
                <div class="grid grid-cols-2 sm:grid-cols-7 gap-2 mb-3">
                    @foreach($days as $col => $lbl)
                    <div class="bg-slate-50 p-2 rounded border">
                        <label class="block text-[11px] font-bold uppercase mb-1">{{ $lbl }}</label>
                        <select name="{{ $col }}" {{ !$canEdit ? 'disabled' : '' }} class="w-full border p-1 rounded text-xs font-semibold bg-white">
                            @foreach($allowedShifts as $sh)
                                @php
                                    $sel = ($myDraft && $myDraft->$col === $sh->name) || (!$myDraft && $col === 'sat_shift' && $sh->is_off_day);
                                @endphp
                                <option value="{{ $sh->name }}" {{ $sel ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
                @if($canEdit)
                <div class="flex flex-wrap justify-between items-center gap-2">
                    <input type="text" name="notes" value="{{ $myDraft->notes ?? '' }}" placeholder="Optional preference note..." class="border p-1.5 rounded text-xs flex-1">
                    <button type="submit" class="bg-[#065f46] text-white font-semibold px-4 py-2 rounded text-xs">Confirm & Lock 7-Day Draft</button>
                </div>
                @endif
            </form>

            <div class="mt-5 pt-4 border-t grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="bg-amber-50/50 p-3 rounded-lg border border-amber-200">
                    <h3 class="text-xs font-bold uppercase text-amber-900 mb-2">Pending Drafts ({{ count($pendingUsers) }})</h3>
                    <div class="space-y-1 max-h-40 overflow-y-auto text-xs">
                        @forelse($pendingUsers as $pUser)
                            <div class="bg-white p-1.5 rounded border flex justify-between">
                                <span class="font-semibold">{{ $pUser->name }}</span>
                                <span class="text-[10px] bg-amber-100 text-amber-800 px-1.5 rounded font-bold">Pending</span>
                            </div>
                        @empty
                            <div class="text-emerald-700 font-semibold">All team drafts submitted!</div>
                        @endforelse
                    </div>
                </div>
                <div class="lg:col-span-2 overflow-x-auto">
                    <h3 class="text-xs font-bold uppercase text-slate-700 mb-2">Team 7-Day Draft Matrix (Read-Only)</h3>
                    <table class="w-full text-[11px] border">
                        <thead><tr class="bg-slate-800 text-white"><th class="p-1.5 text-left">Staff</th><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>@if(Auth::user()->role==='admin')<th>Admin</th>@endif</tr></thead>
                        <tbody>
                            @foreach($team as $member)
                            @php $dRow = $allDrafts->get($member->id); @endphp
                            <tr class="border-b">
                                <td class="p-1.5 font-semibold">{{ $member->name }}</td>
                                @foreach(['sun_shift','mon_shift','tue_shift','wed_shift','thu_shift','fri_shift','sat_shift'] as $c)
                                <td class="p-1 text-center">{{ ($dRow && $dRow->is_submitted) ? explode('(', $dRow->$c)[0] : '-' }}</td>
                                @endforeach
                                @if(Auth::user()->role === 'admin')
                                <td class="p-1 text-center">
                                    @if($dRow)
                                    <form action="{{ route('rosters.draft.unlock', $member->id) }}" method="POST" class="inline">
                                        @csrf <input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="year" value="{{ $year }}"><input type="hidden" name="mode" value="unlock">
                                        <button class="bg-amber-100 text-amber-900 px-1.5 py-0.5 rounded font-bold text-[10px]">Unlock</button>
                                    </form>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="swap-section" class="bg-white p-5 rounded-xl border">
            <h2 class="text-sm font-bold text-slate-900 mb-1">Step 2: Mutual Shift Swap Requisitions (2-Stage Approval)</h2>
            <p class="text-xs text-slate-500 mb-4">Colleague accepts &rarr; Status becomes "Waiting for Admin Approval" &rarr; Admin approves to swap on main roster.</p>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <form action="{{ route('rosters.swap.request') }}" method="POST" class="bg-slate-50 p-3.5 rounded-lg border space-y-2 text-xs">
                    @csrf
                    <div class="font-bold">Request Shift Exchange</div>
                    @if(Auth::user()->role === 'admin')
                    <select name="requester_id" class="w-full border p-1.5 rounded bg-white">
                        @foreach($team as $m) <option value="{{ $m->id }}" {{ $targetUser->id == $m->id ? 'selected' : '' }}>Requester: {{ $m->name }}</option> @endforeach
                    </select>
                    @endif
                    <select name="target_user_id" class="w-full border p-1.5 rounded bg-white" required>
                        @foreach($team as $m) @if($m->id !== Auth::id() || Auth::user()->role === 'admin') <option value="{{ $m->id }}">Swap With: {{ $m->name }}</option> @endif @endforeach
                    </select>
                    <input type="date" name="duty_date" value="{{ date('Y-m-d') }}" class="w-full border p-1.5 rounded bg-white" required>
                    <input type="text" name="reason" placeholder="Reason / mutual note..." class="w-full border p-1.5 rounded bg-white">
                    <button type="submit" class="w-full bg-purple-700 text-white font-bold py-1.5 rounded">Send Swap Request</button>
                </form>

                <div class="lg:col-span-2 overflow-x-auto">
                    <table class="w-full text-xs border">
                        <thead><tr class="bg-slate-800 text-white"><th class="p-2 text-left">Date</th><th class="p-2 text-left">Requester &harr; Colleague</th><th class="p-2 text-center">Shifts</th><th class="p-2 text-center">Status</th><th class="p-2 text-center">Action</th></tr></thead>
                        <tbody>
                            @forelse($swaps as $sw)
                            <tr class="border-b">
                                <td class="p-2 font-bold">{{ $sw->duty_date }}</td>
                                <td class="p-2">{{ $sw->requester->name ?? '' }} &harr; {{ $sw->targetUser->name ?? '' }}</td>
                                <td class="p-2 text-center">{{ explode('(', $sw->requester_shift)[0] }} &harr; {{ explode('(', $sw->target_shift)[0] }}</td>
                                <td class="p-2 text-center">
                                    @if($sw->status === 'pending_colleague') <span class="bg-amber-100 px-2 py-0.5 rounded text-[10px] font-semibold">Waiting for {{ $sw->targetUser->name ?? 'Colleague' }}</span>
                                    @elseif($sw->status === 'colleague_accepted') <span class="bg-blue-100 text-blue-900 px-2 py-0.5 rounded text-[10px] font-bold">Waiting for Admin Approval</span>
                                    @elseif($sw->status === 'admin_approved') <span class="bg-purple-100 text-purple-900 px-2 py-0.5 rounded text-[10px] font-bold">Approved & Swapped</span>
                                    @else <span class="bg-rose-100 text-rose-800 px-2 py-0.5 rounded text-[10px]">Rejected</span>
                                    @endif
                                </td>
                                <td class="p-2 text-center">
                                    @if($sw->status === 'pending_colleague' && (Auth::id() == $sw->target_user_id || Auth::user()->role === 'admin'))
                                    <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST" class="inline">
                                        @csrf <input type="hidden" name="action" value="colleague_accept">
                                        <button class="bg-emerald-600 text-white px-2 py-0.5 rounded text-[10px] font-bold">Accept</button>
                                    </form>
                                    <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST" class="inline">
                                        @csrf <input type="hidden" name="action" value="colleague_reject">
                                        <button class="bg-rose-600 text-white px-2 py-0.5 rounded text-[10px]">Reject</button>
                                    </form>
                                    @endif
                                    @if($sw->status === 'colleague_accepted' && Auth::user()->role === 'admin')
                                    <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST" class="inline">
                                        @csrf <input type="hidden" name="action" value="admin_approve">
                                        <button class="bg-purple-700 text-white px-2 py-0.5 rounded text-[10px] font-bold">Admin Approve</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty <tr><td colspan="5" class="p-4 text-center text-slate-400">No swap requests yet.</td></tr> @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="daily-attendance" class="bg-white p-5 rounded-xl border">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Step 3: Daily In/Out Time-Clock, Overtime & Correction Sheet ({{ $targetUser->name }})</h2>
                    <p class="text-xs text-slate-500">Previous month entries lock automatically after the 2nd of the month at 12:00 AM.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('report.csv', ['user_id' => $targetUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-slate-100 border px-3 py-1.5 rounded text-xs font-semibold">Export CSV</a>
                    <a href="{{ route('report.print', ['user_id' => $targetUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-[#065f46] text-white px-3.5 py-1.5 rounded text-xs font-semibold">1-Page A4 Print</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border">
                    <thead><tr class="bg-slate-800 text-white"><th class="p-2 text-left">Date & Day</th><th class="p-2 text-left">Regular Shift & OT</th><th class="p-2 text-center">In-Time</th><th class="p-2 text-center">Out-Time</th><th class="p-2 text-center">+Overtime</th><th class="p-2 text-center">Status</th><th class="p-2 text-center">Update / Override</th></tr></thead>
                    <tbody>
                        @foreach($myMonthlyDuties as $duty)
                        @php
                            $isLocked = \App\Http\Controllers\FrontDeskPortalController::isDateLockedForAttendance($duty->duty_date, $setting);
                        @endphp
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-2 font-semibold">{{ $duty->duty_date }} <span class="text-[10px] text-slate-500 block">{{ $duty->day_name }}</span></td>
                            <td class="p-2">
                                <span class="font-bold">{{ $duty->shift_name }}</span>
                                @if($duty->is_swapped) <span class="text-[10px] text-purple-700 font-bold block">&harr; {{ $duty->swap_info }}</span> @endif
                                @if($duty->has_overtime) <span class="text-[10px] bg-indigo-50 text-indigo-800 border px-1.5 py-0.5 rounded font-bold inline-block mt-0.5">+OT: {{ $duty->overtime_shift }} ({{ $duty->overtime_hours }}h)</span> @endif
                            </td>
                            <td class="p-2 text-center">
                                @if($duty->is_off_day) <span class="text-rose-600 font-semibold">Off</span>
                                @elseif(!$duty->in_locked && !$isLocked)
                                    <form action="{{ route('attendance.punch', $duty->id) }}" method="POST" class="flex justify-center gap-1" onsubmit="return confirm('Save In-Time? It will lock immediately.');">
                                        @csrf <input type="hidden" name="punch_type" value="in_time">
                                        <input type="time" name="in_time" class="border rounded p-1 text-xs" required>
                                        <button class="bg-[#065f46] text-white px-2 py-0.5 rounded font-semibold">Save</button>
                                    </form>
                                @else <span class="font-bold">{{ $duty->in_time ?: '--:--' }}</span> <i class="fa-solid fa-lock text-slate-400 text-[9px]"></i> @endif
                            </td>
                            <td class="p-2 text-center">
                                @if($duty->is_off_day) <span class="text-rose-600 font-semibold">Off</span>
                                @elseif(!$duty->out_locked && !$isLocked)
                                    <form action="{{ route('attendance.punch', $duty->id) }}" method="POST" class="flex justify-center gap-1" onsubmit="return confirm('Save Out-Time? It will lock immediately.');">
                                        @csrf <input type="hidden" name="punch_type" value="out_time">
                                        <input type="time" name="out_time" class="border rounded p-1 text-xs" required>
                                        <button class="bg-slate-800 text-white px-2 py-0.5 rounded font-semibold">Save</button>
                                    </form>
                                @else <span class="font-bold">{{ $duty->out_time ?: '--:--' }}</span> <i class="fa-solid fa-lock text-slate-400 text-[9px]"></i> @endif
                            </td>
                            <td class="p-2 text-center">
                                @if(!$isLocked || Auth::user()->role === 'admin')
                                <form action="{{ route('attendance.punch', $duty->id) }}" method="POST" class="flex justify-center gap-1">
                                    @csrf <input type="hidden" name="punch_type" value="overtime">
                                    <input type="text" name="overtime_shift" value="{{ $duty->overtime_shift }}" placeholder="3PM-11PM" class="border rounded p-1 text-[10px] w-20">
                                    <input type="number" step="0.5" name="overtime_hours" value="{{ $duty->overtime_hours ?: 8 }}" class="border rounded p-1 text-[10px] w-10">
                                    <button class="bg-indigo-600 text-white px-1.5 py-0.5 rounded text-[10px] font-bold">+OT</button>
                                </form>
                                @endif
                            </td>
                            <td class="p-2 text-center">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $duty->attendance_status === 'Late' ? 'bg-amber-100 text-amber-900' : ($duty->attendance_status === 'Present' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100') }}">{{ $duty->attendance_status }}</span>
                            </td>
                            <td class="p-2 text-center">
                                @if($duty->update_status === 'pending')
                                    <span class="bg-amber-50 border border-amber-300 text-amber-900 px-1.5 py-0.5 rounded text-[9px] font-bold block mb-1">Waiting for Approval / Contact Admin</span>
                                @endif
                                @if(Auth::user()->role === 'admin')
                                <details class="inline-block text-left">
                                    <summary class="cursor-pointer bg-amber-400 text-slate-950 px-2 py-0.5 rounded font-bold text-[10px]">Override</summary>
                                    <form action="{{ route('attendance.admin.action', $duty->id) }}" method="POST" class="mt-1 p-2.5 bg-white border rounded-lg shadow-lg space-y-1.5 w-56 absolute right-6 z-20">
                                        @csrf <input type="hidden" name="action" value="direct_override">
                                        <div class="font-bold text-[10px] uppercase">Super Admin Edit</div>
                                        <select name="shift_name" class="w-full border p-1 rounded text-xs">
                                            @foreach($allShifts as $s) <option value="{{ $s->name }}" {{ $duty->shift_name === $s->name ? 'selected' : '' }}>{{ $s->name }}</option> @endforeach
                                        </select>
                                        <div class="grid grid-cols-2 gap-1">
                                            <input type="time" name="in_time" value="{{ $duty->in_time }}" class="border p-1 rounded text-xs">
                                            <input type="time" name="out_time" value="{{ $duty->out_time }}" class="border p-1 rounded text-xs">
                                        </div>
                                        <select name="attendance_status" class="w-full border p-1 rounded text-xs">
                                            <option value="Auto">Auto Status</option><option value="Present">Present</option><option value="Late">Late</option><option value="Absent">Absent</option><option value="Off Day">Off Day</option>
                                        </select>
                                        <input type="text" name="remarks" value="{{ $duty->remarks }}" placeholder="Remarks..." class="w-full border p-1 rounded text-xs">
                                        <button type="submit" class="w-full bg-[#065f46] text-white py-1 rounded text-xs font-bold">Save Override</button>
                                    </form>
                                </details>
                                @elseif(!$isLocked)
                                <details class="inline-block text-left">
                                    <summary class="cursor-pointer bg-slate-100 border px-2 py-0.5 rounded font-semibold text-[10px]">Request Update</summary>
                                    <form action="{{ route('attendance.request.update', $duty->id) }}" method="POST" class="mt-1 p-2 bg-white border rounded-lg shadow-lg space-y-1 w-48 absolute right-6 z-20">
                                        @csrf
                                        <input type="time" name="req_in_time" value="{{ $duty->in_time }}" class="w-full border p-1 rounded text-xs">
                                        <input type="time" name="req_out_time" value="{{ $duty->out_time }}" class="w-full border p-1 rounded text-xs">
                                        <input type="text" name="req_reason" placeholder="Reason..." class="w-full border p-1 rounded text-xs" required>
                                        <button type="submit" class="w-full bg-[#065f46] text-white py-1 rounded text-[10px] font-bold">Submit to Admin</button>
                                    </form>
                                </details>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
