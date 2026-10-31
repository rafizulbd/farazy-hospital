<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster, Swap & Attendance Portal | {{ $setting->hospital_name }}</title>
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
        <!-- Toast Alerts -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-[#065f46] text-emerald-950 font-semibold rounded-xl shadow-sm text-xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-[#065f46] text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <span class="text-[10px] font-bold uppercase text-emerald-700">Updated</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-rose-50 border-l-4 border-rose-600 text-rose-950 font-semibold rounded-xl shadow-sm text-xs flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Top Portal Header & Filter Controls -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="bg-emerald-50 border border-emerald-200 text-[#065f46] text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full">
                        Workforce Operations
                    </span>
                    <span class="text-xs font-bold text-slate-400">{{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</span>
                </div>
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 mt-1">
                    Duty Roster, Mutual Shift Exchange & Time-Clock Portal
                </h1>
                <p class="text-xs text-slate-500">6 Days Duty + 1 Day Off Weekly Pattern &bull; 2-Stage Mutual Swap &bull; Auto-Locked Daily Attendance</p>
            </div>

            <form method="GET" action="{{ route('rosters.index') }}" class="flex flex-wrap items-center gap-2 text-xs">
                @if(Auth::user()->role === 'admin')
                <div class="flex items-center bg-emerald-50 border border-emerald-300 rounded-xl px-2.5 py-1.5">
                    <i class="fa-solid fa-user-gear text-[#065f46] mr-1.5"></i>
                    <select name="user_id" onchange="this.form.submit()" class="font-extrabold bg-transparent text-emerald-950 outline-none">
                        @foreach($team as $m)
                            <option value="{{ $m->id }}" {{ $targetUser->id == $m->id ? 'selected' : '' }}>
                                Staff: {{ $m->name }} ({{ $m->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <select name="month" onchange="this.form.submit()" class="border border-slate-300 rounded-xl px-3 py-2 font-bold bg-slate-50">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>

                <select name="year" onchange="this.form.submit()" class="border border-slate-300 rounded-xl px-3 py-2 font-bold bg-slate-50">
                    @for($y = 2025; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <a href="{{ route('report.print', ['user_id' => $targetUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-bold px-3.5 py-2 rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> <span>A4 Sheet</span>
                </a>
            </form>
        </div>

        <!-- In-Charge (Admin) Quick Merge & Phase Bar -->
        @if(Auth::user()->role === 'admin')
        <div class="bg-gradient-to-r from-[#053b2c] via-[#064e3b] to-[#042f23] text-white p-5 rounded-2xl shadow-md border border-emerald-700 flex flex-wrap justify-between items-center gap-4">
            <div>
                <span class="bg-amber-400 text-slate-950 text-[10px] font-extrabold px-2 py-0.5 rounded uppercase tracking-wider">
                    <i class="fa-solid fa-crown mr-1"></i> In-Charge Governance Bar
                </span>
                <h3 class="text-sm md:text-base font-extrabold text-white mt-1">Monthly Roster Merge & Window Phase Controller</h3>
                <p class="text-[11px] text-emerald-200">29th: Draft Submission Open &bull; 30th: Merge Drafts & Open Mutual Swap &bull; 31st: Verify & Lock Final Roster</p>
            </div>

            <form action="{{ route('rosters.merge') }}" method="POST" class="flex flex-wrap items-center gap-2 text-xs">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <select name="next_phase" class="bg-[#022c22] border border-emerald-500 text-white font-bold px-3 py-2.5 rounded-xl outline-none">
                    <option value="draft_open" {{ $setting->roster_phase === 'draft_open' ? 'selected' : '' }}>Phase 1: Draft Open (29th)</option>
                    <option value="merge_review" {{ $setting->roster_phase === 'merge_review' ? 'selected' : '' }}>Phase 2: Merged & Swap Open (30th)</option>
                    <option value="final_verified" {{ $setting->roster_phase === 'final_verified' ? 'selected' : '' }}>Phase 3: Final Verified (31st)</option>
                    <option value="closed" {{ $setting->roster_phase === 'closed' ? 'selected' : '' }}>Portal Locked / Closed</option>
                </select>
                <input type="text" name="verified_by" value="{{ $setting->verified_by }}" placeholder="Verified By Authority" class="bg-[#022c22] border border-emerald-500 text-white px-3 py-2.5 rounded-xl outline-none">
                <button type="submit" class="bg-amber-400 hover:bg-amber-500 text-slate-950 font-extrabold px-4 py-2.5 rounded-xl shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> <span>Merge & Apply Phase</span>
                </button>
            </form>
        </div>
        @endif

        <!-- STEP 1: 7-DAY WEEKLY DRAFT ROSTER BUILDER -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="h-6 w-6 rounded-lg bg-[#065f46] text-white text-xs flex items-center justify-center font-black">1</span>
                        <span>7-Day Weekly Draft Roster Builder ({{ $targetUser->name }})</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Select <strong>6 Days Duty and 1 Day Off</strong>. Saving locks your draft and generates your full monthly schedule.</p>
                </div>

                <!-- Live 6+1 Rule Validation Badge -->
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                    <span id="rule-counter-badge" class="px-3 py-1.5 rounded-xl border bg-slate-50 text-slate-700">
                        Checking pattern...
                    </span>
                    <span class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-1.5 rounded-xl">
                        Submitted: {{ count($submittedUsers) }}
                    </span>
                    <span class="bg-amber-50 border border-amber-200 text-amber-800 px-3 py-1.5 rounded-xl">
                        Pending: {{ count($pendingUsers) }}
                    </span>
                </div>
            </div>

            @php
                $daysMap = [
                    'sun_shift' => 'Sunday', 'mon_shift' => 'Monday', 'tue_shift' => 'Tuesday',
                    'wed_shift' => 'Wednesday', 'thu_shift' => 'Thursday', 'fri_shift' => 'Friday', 'sat_shift' => 'Saturday'
                ];
                $canEditDraft = ($setting->roster_phase === 'draft_open' && (!$myDraft || !$myDraft->is_submitted)) || Auth::user()->role === 'admin';
            @endphp

            @if($myDraft && $myDraft->is_submitted && Auth::user()->role !== 'admin')
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-950 rounded-xl text-xs font-semibold mb-4 flex justify-between items-center">
                    <span><i class="fa-solid fa-lock text-[#065f46] mr-1.5"></i> Your 7-Day Draft Roster is submitted and locked. View the team draft matrix below.</span>
                    <span class="bg-[#065f46] text-white px-2.5 py-0.5 rounded-full text-[10px] font-bold">Locked</span>
                </div>
            @endif

            <form action="{{ route('rosters.draft.save') }}" method="POST" onsubmit="return confirm('Confirm Draft Roster submission? Make sure you selected 6 Days Duty and 1 Off Day. Once saved, it will lock automatically.');">
                @csrf
                <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-4">
                    @foreach($daysMap as $col => $dayLabel)
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 hover:border-emerald-400 transition">
                        <label class="block text-[11px] font-extrabold uppercase text-slate-700 mb-1.5">{{ $dayLabel }}</label>
                        <select name="{{ $col }}" onchange="updateRuleCounter()" {{ !$canEditDraft ? 'disabled' : '' }} class="draft-day-select w-full border border-slate-300 p-2 rounded-lg text-xs font-bold bg-white outline-none focus:ring-2 focus:ring-[#065f46]">
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

                @if($canEditDraft)
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <input type="text" name="notes" value="{{ $myDraft->notes ?? '' }}" placeholder="Optional note for Front Desk In-Charge..." class="border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs flex-1 bg-slate-50 focus:bg-white outline-none">
                    <button type="submit" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-extrabold px-6 py-2.5 rounded-xl text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Confirm & Lock 7-Day Draft</span>
                    </button>
                </div>
                @endif
            </form>

            <!-- Pending Submissions & Team Draft Matrix -->
            <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-1 lg:grid-cols-12 gap-5">
                <div class="lg:col-span-4 bg-amber-50/50 p-4 rounded-2xl border border-amber-200">
                    <h3 class="text-xs font-extrabold uppercase text-amber-900 mb-2.5 flex items-center justify-between">
                        <span><i class="fa-regular fa-clock mr-1"></i> Pending Drafts</span>
                        <span class="bg-amber-200 text-amber-950 px-2 py-0.5 rounded-full text-[10px]">{{ count($pendingUsers) }}</span>
                    </h3>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1 text-xs">
                        @forelse($pendingUsers as $pUser)
                            <div class="bg-white p-2 rounded-xl border border-amber-200 flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $pUser->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $pUser->employee_id }}</span>
                                </div>
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded">Pending</span>
                            </div>
                        @empty
                            <div class="text-xs font-bold text-emerald-700 py-4 text-center">
                                <i class="fa-solid fa-circle-check block text-lg mb-1"></i>
                                All team members have submitted their drafts!
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="lg:col-span-8 overflow-x-auto">
                    <h3 class="text-xs font-extrabold uppercase text-slate-700 mb-2.5">
                        <i class="fa-solid fa-table-list text-[#065f46] mr-1"></i> Team 7-Day Merged Draft Matrix (Read-Only)
                    </h3>
                    <table class="w-full border-collapse text-[11px] border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-800 text-white uppercase">
                                <th class="p-2 text-left">Staff Member</th>
                                <th class="p-2 text-center">Sun</th>
                                <th class="p-2 text-center">Mon</th>
                                <th class="p-2 text-center">Tue</th>
                                <th class="p-2 text-center">Wed</th>
                                <th class="p-2 text-center">Thu</th>
                                <th class="p-2 text-center">Fri</th>
                                <th class="p-2 text-center">Sat</th>
                                @if(Auth::user()->role === 'admin')
                                <th class="p-2 text-center">Manage</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($team as $member)
                            @php $dRow = $allDrafts->get($member->id); @endphp
                            <tr class="border-b hover:bg-slate-50">
                                <td class="p-2 font-bold text-slate-900">{{ $member->name }}</td>
                                @foreach(['sun_shift','mon_shift','tue_shift','wed_shift','thu_shift','fri_shift','sat_shift'] as $c)
                                <td class="p-1.5 text-center">
                                    @if($dRow && $dRow->is_submitted)
                                        <span class="px-1.5 py-0.5 rounded font-bold {{ str_contains(strtolower($dRow->$c), 'off') ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-900' }}">
                                            {{ explode('(', $dRow->$c)[0] }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                @endforeach
                                @if(Auth::user()->role === 'admin')
                                <td class="p-1.5 text-center">
                                    @if($dRow)
                                    <div class="flex justify-center gap-1">
                                        <form action="{{ route('rosters.draft.unlock', $member->id) }}" method="POST">
                                            @csrf <input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="year" value="{{ $year }}"><input type="hidden" name="mode" value="unlock">
                                            <button class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded font-bold" title="Unlock Draft"><i class="fa-solid fa-unlock"></i></button>
                                        </form>
                                        <form action="{{ route('rosters.draft.unlock', $member->id) }}" method="POST" onsubmit="return confirm('Delete this member\'s roster for this month?');">
                                            @csrf <input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="year" value="{{ $year }}"><input type="hidden" name="mode" value="delete">
                                            <button class="bg-rose-100 text-rose-800 px-2 py-0.5 rounded font-bold" title="Delete Roster"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
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

        <!-- STEP 2: 2-STAGE MUTUAL SHIFT SWAP PORTAL -->
        <div id="swap-section" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-purple-700 text-white text-xs flex items-center justify-center font-black">2</span>
                    <span>Mutual Duty Change / Shift Exchange Portal (2-Stage Approval)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Stage 1: Colleague accepts your request (Status becomes <strong>Waiting for Admin Approval</strong>) &rarr; Stage 2: Admin approves to swap shifts on the main roster.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Request Form -->
                <form action="{{ route('rosters.swap.request') }}" method="POST" class="lg:col-span-4 bg-purple-50/50 p-4 rounded-2xl border border-purple-200 space-y-3 text-xs">
                    @csrf
                    <h3 class="font-extrabold text-purple-950 text-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-people-arrows text-purple-700"></i> Send Shift Swap Request
                    </h3>
                    @if(Auth::user()->role === 'admin')
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Requester Staff</label>
                        <select name="requester_id" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold bg-white">
                            @foreach($team as $m)
                                <option value="{{ $m->id }}" {{ $targetUser->id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Select Colleague to Swap With</label>
                        <select name="target_user_id" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold bg-white" required>
                            @foreach($team as $m)
                                @if($m->id !== Auth::id() || Auth::user()->role === 'admin')
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->designation }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Duty Date to Exchange</label>
                        <input type="date" name="duty_date" value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold bg-white" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Reason / Mutual Note</label>
                        <input type="text" name="reason" placeholder="Personal emergency / mutual discussion..." class="w-full border border-slate-300 p-2.5 rounded-xl bg-white">
                    </div>
                    <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-extrabold py-2.5 rounded-xl shadow-sm transition">
                        <i class="fa-regular fa-paper-plane mr-1"></i> Send Swap Request
                    </button>
                </form>

                <!-- Swap Requests Table -->
                <div class="lg:col-span-8 overflow-x-auto">
                    <table class="w-full border-collapse text-xs border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-800 text-white uppercase text-[11px]">
                                <th class="p-2.5 text-left">Duty Date</th>
                                <th class="p-2.5 text-left">Requester &harr; Colleague</th>
                                <th class="p-2.5 text-center">Shift Exchange</th>
                                <th class="p-2.5 text-center">Approval Stage</th>
                                <th class="p-2.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($swaps as $sw)
                            <tr class="border-b hover:bg-slate-50">
                                <td class="p-2.5 font-extrabold text-slate-900">{{ \Carbon\Carbon::parse($sw->duty_date)->format('d M Y') }}</td>
                                <td class="p-2.5">
                                    <strong>{{ $sw->requester->name ?? '' }}</strong>
                                    <span class="text-purple-600 font-bold mx-1">&harr;</span>
                                    <strong>{{ $sw->targetUser->name ?? '' }}</strong>
                                    @if($sw->reason)
                                        <span class="block text-[10px] text-slate-500 italic">"{{ $sw->reason }}"</span>
                                    @endif
                                </td>
                                <td class="p-2.5 text-center">
                                    <span class="bg-slate-100 px-2 py-0.5 rounded font-semibold">{{ explode('(', $sw->requester_shift)[0] }}</span>
                                    &harr;
                                    <span class="bg-slate-100 px-2 py-0.5 rounded font-semibold">{{ explode('(', $sw->target_shift)[0] }}</span>
                                </td>
                                <td class="p-2.5 text-center">
                                    @if($sw->status === 'pending_colleague')
                                        <span class="bg-amber-50 border border-amber-200 text-amber-900 px-2.5 py-0.5 rounded-full font-bold text-[10px]">
                                            Waiting for {{ $sw->targetUser->name ?? 'Colleague' }}
                                        </span>
                                    @elseif($sw->status === 'colleague_accepted')
                                        <span class="bg-blue-50 border border-blue-200 text-blue-900 px-2.5 py-0.5 rounded-full font-extrabold text-[10px]">
                                            Waiting for Admin Approval
                                        </span>
                                    @elseif($sw->status === 'admin_approved')
                                        <span class="bg-purple-100 border border-purple-300 text-purple-900 px-2.5 py-0.5 rounded-full font-extrabold text-[10px]">
                                            Approved & Swapped
                                        </span>
                                    @else
                                        <span class="bg-rose-50 text-rose-700 px-2.5 py-0.5 rounded-full font-bold text-[10px]">Rejected</span>
                                    @endif
                                </td>
                                <td class="p-2.5 text-center">
                                    @if($sw->status === 'pending_colleague' && (Auth::id() == $sw->target_user_id || Auth::user()->role === 'admin'))
                                        <div class="flex justify-center gap-1">
                                            <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                                @csrf <input type="hidden" name="action" value="colleague_accept">
                                                <button class="bg-emerald-600 text-white px-2.5 py-1 rounded-lg font-bold text-[11px]">Accept</button>
                                            </form>
                                            <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                                @csrf <input type="hidden" name="action" value="colleague_reject">
                                                <button class="bg-rose-600 text-white px-2.5 py-1 rounded-lg font-bold text-[11px]">Reject</button>
                                            </form>
                                        </div>
                                    @endif
                                    @if($sw->status === 'colleague_accepted' && Auth::user()->role === 'admin')
                                        <div class="flex justify-center gap-1 mt-1">
                                            <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                                @csrf <input type="hidden" name="action" value="admin_approve">
                                                <button class="bg-purple-700 text-white px-2.5 py-1 rounded-lg font-extrabold text-[11px]">Admin Approve</button>
                                            </form>
                                            <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                                @csrf <input type="hidden" name="action" value="admin_reject">
                                                <button class="bg-rose-600 text-white px-2.5 py-1 rounded-lg font-bold text-[11px]">Reject</button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-400">No shift swap requests found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 3: DAILY ATTENDANCE, OVERTIME & ADMIN OVERRIDE SHEET -->
        <div id="daily-attendance" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-5 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="h-6 w-6 rounded-lg bg-[#065f46] text-white text-xs flex items-center justify-center font-black">3</span>
                        <span>Daily In-Time, Out-Time, Overtime & Correction Sheet ({{ $targetUser->name }})</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        All previous month entries lock automatically after the <strong>2nd of the month at 12:00 AM</strong>.
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('report.csv', ['user_id' => $targetUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-bold px-3.5 py-2 rounded-xl text-xs">
                        <i class="fa-solid fa-file-csv mr-1 text-emerald-700"></i> Export CSV
                    </a>
                    <a href="{{ route('report.print', ['user_id' => $targetUser->id, 'month' => $month, 'year' => $year]) }}" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm">
                        <i class="fa-solid fa-print mr-1"></i> 1-Page A4 HR Print
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead>
                        <tr class="bg-slate-800 text-white uppercase text-[11px]">
                            <th class="p-3 text-left">Date & Day</th>
                            <th class="p-3 text-left">Assigned Shift & Overtime</th>
                            <th class="p-3 text-center">In-Time</th>
                            <th class="p-3 text-center">Out-Time</th>
                            <th class="p-3 text-center">Log Overtime</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Correction / Override</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myMonthlyDuties as $duty)
                        @php
                            $isLocked = \App\Http\Controllers\FrontDeskPortalController::isDateLockedForAttendance($duty->duty_date, $setting);
                            $rowBg = 'hover:bg-slate-50';
                            if ($duty->attendance_status === 'Late') $rowBg = 'bg-amber-50/50';
                            elseif ($duty->attendance_status === 'Absent') $rowBg = 'bg-rose-50/60';
                            elseif ($duty->is_swapped) $rowBg = 'bg-purple-50/40';
                            elseif ($duty->is_off_day) $rowBg = 'bg-slate-50/80';
                        @endphp
                        <tr class="border-b border-slate-200 {{ $rowBg }}">
                            <td class="p-2.5 font-bold text-slate-900">
                                {{ \Carbon\Carbon::parse($duty->duty_date)->format('d M Y') }}
                                <span class="block text-[10px] text-slate-500 font-normal">{{ $duty->day_name }}</span>
                            </td>
                            <td class="p-2.5">
                                <span class="font-extrabold text-slate-900">{{ $duty->shift_name }}</span>
                                @if($duty->is_swapped)
                                    <span class="block text-[10px] font-bold text-purple-700">&harr; {{ $duty->swap_info }}</span>
                                @endif
                                @if($duty->has_overtime)
                                    <span class="inline-block mt-1 bg-indigo-50 text-indigo-900 border border-indigo-200 px-2 py-0.5 rounded text-[10px] font-bold">
                                        +OT: {{ $duty->overtime_shift }} ({{ $duty->overtime_hours }}h)
                                    </span>
                                @endif
                            </td>

                            <td class="p-2.5 text-center">
                                @if($duty->is_off_day)
                                    <span class="text-rose-600 font-bold text-[11px]">Off Day</span>
                                @elseif(!$duty->in_locked && !$isLocked)
                                    <form action="{{ route('attendance.punch', $duty->id) }}" method="POST" class="flex items-center justify-center gap-1" onsubmit="return confirm('Confirm In-Time? It will lock immediately.');">
                                        @csrf <input type="hidden" name="punch_type" value="in_time">
                                        <input type="time" name="in_time" class="border border-slate-300 rounded-lg p-1 text-xs font-bold" required>
                                        <button class="bg-[#065f46] text-white px-2.5 py-1 rounded-lg font-bold">Save</button>
                                    </form>
                                @else
                                    <span class="font-extrabold text-slate-800">{{ $duty->in_time ?: '--:--' }}</span>
                                    <i class="fa-solid fa-lock text-slate-400 text-[10px] ml-1"></i>
                                @endif
                            </td>

                            <td class="p-2.5 text-center">
                                @if($duty->is_off_day)
                                    <span class="text-rose-600 font-bold text-[11px]">Off Day</span>
                                @elseif(!$duty->out_locked && !$isLocked)
                                    <form action="{{ route('attendance.punch', $duty->id) }}" method="POST" class="flex items-center justify-center gap-1" onsubmit="return confirm('Confirm Out-Time? It will lock immediately.');">
                                        @csrf <input type="hidden" name="punch_type" value="out_time">
                                        <input type="time" name="out_time" class="border border-slate-300 rounded-lg p-1 text-xs font-bold" required>
                                        <button class="bg-slate-800 text-white px-2.5 py-1 rounded-lg font-bold">Save</button>
                                    </form>
                                @else
                                    <span class="font-extrabold text-slate-800">{{ $duty->out_time ?: '--:--' }}</span>
                                    <i class="fa-solid fa-lock text-slate-400 text-[10px] ml-1"></i>
                                @endif
                            </td>

                            <td class="p-2.5 text-center">
                                @if(!$isLocked || Auth::user()->role === 'admin')
                                <form action="{{ route('attendance.punch', $duty->id) }}" method="POST" class="flex items-center justify-center gap-1">
                                    @csrf <input type="hidden" name="punch_type" value="overtime">
                                    <input type="text" name="overtime_shift" value="{{ $duty->overtime_shift }}" placeholder="3PM-11PM" class="border border-slate-300 rounded-lg p-1 text-[11px] w-24">
                                    <input type="number" step="0.5" name="overtime_hours" value="{{ $duty->overtime_hours ?: 8 }}" class="border border-slate-300 rounded-lg p-1 text-[11px] w-12">
                                    <button class="bg-indigo-600 text-white px-2 py-1 rounded-lg font-bold text-[10px]">+OT</button>
                                </form>
                                @else
                                    <span class="text-slate-400 text-[10px]">Locked</span>
                                @endif
                            </td>

                            <td class="p-2.5 text-center">
                                @if($duty->attendance_status === 'Late')
                                    <span class="bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-full font-extrabold text-[10px]">LATE ({{ $duty->late_minutes }}m)</span>
                                @elseif($duty->attendance_status === 'Present')
                                    <span class="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 rounded-full font-extrabold text-[10px]">PRESENT</span>
                                @elseif($duty->attendance_status === 'Absent')
                                    <span class="bg-rose-600 text-white px-2 py-0.5 rounded-full font-extrabold text-[10px]">ABSENT</span>
                                @elseif($duty->is_off_day)
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-full font-extrabold text-[10px]">OFF DAY</span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-semibold text-[10px]">{{ $duty->attendance_status }}</span>
                                @endif
                            </td>

                            <td class="p-2.5 text-center">
                                @if($duty->update_status === 'pending')
                                    <span class="bg-amber-50 border border-amber-300 text-amber-900 px-2 py-0.5 rounded text-[10px] font-bold block mb-1">
                                        Waiting for Approval
                                    </span>
                                @endif

                                @if(Auth::user()->role === 'admin')
                                    <details class="inline-block text-left relative">
                                        <summary class="cursor-pointer bg-amber-400 hover:bg-amber-500 text-slate-950 px-2.5 py-1 rounded-lg font-extrabold text-[10px] list-none">
                                            <i class="fa-solid fa-sliders mr-1"></i> Override
                                        </summary>
                                        <form action="{{ route('attendance.admin.action', $duty->id) }}" method="POST" class="mt-1 p-3 bg-white border border-slate-300 rounded-2xl shadow-2xl space-y-2 w-60 right-0 absolute z-30">
                                            @csrf <input type="hidden" name="action" value="direct_override">
                                            <div class="text-[10px] font-extrabold uppercase text-slate-700 border-b pb-1">Super Admin Direct Edit</div>
                                            <select name="shift_name" class="w-full border p-1.5 rounded-lg text-xs font-semibold">
                                                @foreach($allShifts as $s)
                                                    <option value="{{ $s->name }}" {{ $duty->shift_name === $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="grid grid-cols-2 gap-1">
                                                <input type="time" name="in_time" value="{{ $duty->in_time }}" class="border p-1.5 rounded-lg text-xs">
                                                <input type="time" name="out_time" value="{{ $duty->out_time }}" class="border p-1.5 rounded-lg text-xs">
                                            </div>
                                            <select name="attendance_status" class="w-full border p-1.5 rounded-lg text-xs font-bold">
                                                <option value="Auto">Auto Calculate Status</option>
                                                <option value="Present" {{ $duty->attendance_status === 'Present' ? 'selected' : '' }}>Mark Present</option>
                                                <option value="Late" {{ $duty->attendance_status === 'Late' ? 'selected' : '' }}>Mark Late</option>
                                                <option value="Absent" {{ $duty->attendance_status === 'Absent' ? 'selected' : '' }}>Mark Absent</option>
                                                <option value="Off Day" {{ $duty->attendance_status === 'Off Day' ? 'selected' : '' }}>Mark Off Day</option>
                                            </select>
                                            <input type="text" name="remarks" value="{{ $duty->remarks }}" placeholder="Admin remarks..." class="w-full border p-1.5 rounded-lg text-xs">
                                            <button type="submit" class="w-full bg-[#065f46] text-white font-bold py-1.5 rounded-xl text-xs">Save Override</button>
                                        </form>
                                    </details>
                                @elseif(!$isLocked)
                                    <details class="inline-block text-left relative">
                                        <summary class="cursor-pointer bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 px-2.5 py-1 rounded-lg font-bold text-[10px] list-none">
                                            Request Update
                                        </summary>
                                        <form action="{{ route('attendance.request.update', $duty->id) }}" method="POST" class="mt-1 p-3 bg-white border border-slate-300 rounded-2xl shadow-2xl space-y-1.5 w-52 right-0 absolute z-30">
                                            @csrf
                                            <div class="text-[10px] font-bold text-slate-800 border-b pb-1">Request Time Correction</div>
                                            <input type="time" name="req_in_time" value="{{ $duty->in_time }}" class="w-full border p-1.5 rounded-lg text-xs">
                                            <input type="time" name="req_out_time" value="{{ $duty->out_time }}" class="w-full border p-1.5 rounded-lg text-xs">
                                            <input type="text" name="req_reason" placeholder="Reason for update..." class="w-full border p-1.5 rounded-lg text-xs" required>
                                            <button type="submit" class="w-full bg-[#065f46] text-white font-bold py-1.5 rounded-xl text-[10px]">Submit to Admin</button>
                                        </form>
                                    </details>
                                @else
                                    <span class="text-rose-600 font-bold text-[10px]">Locked</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="p-8 text-center text-slate-400">No daily roster generated yet. Submit your 7-Day Draft above first.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function updateRuleCounter() {
            var selects = document.querySelectorAll('.draft-day-select');
            var offCount = 0;
            var dutyCount = 0;
            selects.forEach(function(s) {
                if (s.value.toLowerCase().indexOf('off') !== -1) {
                    offCount++;
                } else {
                    dutyCount++;
                }
            });
            var badge = document.getElementById('rule-counter-badge');
            if (!badge) return;
            if (dutyCount === 6 && offCount === 1) {
                badge.className = 'px-3 py-1.5 rounded-xl border bg-emerald-50 border-emerald-300 text-emerald-900 font-extrabold';
                badge.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Valid Pattern: 6 Duty + 1 Off Day';
            } else {
                badge.className = 'px-3 py-1.5 rounded-xl border bg-rose-50 border-rose-300 text-rose-800 font-extrabold';
                badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-1"></i> Selected: ' + dutyCount + ' Duty & ' + offCount + ' Off (Need 6+1)';
            }
        }
        document.addEventListener('DOMContentLoaded', updateRuleCounter);
    </script>
</body>
</html>
