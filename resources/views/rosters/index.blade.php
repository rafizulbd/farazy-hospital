<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Roster & Swap Portal - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen p-6 font-sans">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Top Navigation Header -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-slate-900">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard & Calendar
                </a>
                <div>
                    <h1 class="text-2xl font-black text-slate-800">Farazy Hospital — Smart Duty Roster Portal</h1>
                    <p class="text-xs text-slate-500">{{ $setting->validity_label }} | Logged in: <strong>{{ Auth::user()->name }}</strong></p>
                </div>
            </div>
            <button onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold px-4 py-2 rounded-xl text-sm border">
                <i class="fa-solid fa-print mr-1"></i> Print / Save PDF
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-100 border-l-4 border-emerald-600 text-emerald-900 font-bold rounded-xl shadow-sm">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-rose-100 border-l-4 border-rose-600 text-rose-900 font-bold rounded-xl shadow-sm">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- ADMIN CONTROL PANEL (Only visible to Admin) -->
        @if(Auth::user()->role === 'admin')
        <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-xl border-t-4 border-amber-400">
            <h2 class="text-lg font-extrabold text-amber-400 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-shield"></i> Admin Master Control — Notice, Validity & 3-Phase Access Control
            </h2>
            <form action="{{ route('rosters.settings') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Roster Title</label>
                        <input type="text" name="title" value="{{ $setting->title }}" class="w-full p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Validity Period Mention (e.g. 2-3 Months)</label>
                        <input type="text" name="validity_label" value="{{ $setting->validity_label }}" class="w-full p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-emerald-300 font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-amber-300 mb-1">Active Access Phase (Strict Lock Control)</label>
                        <select name="current_phase" class="w-full p-2.5 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-sm">
                            <option value="draft_open" {{ $setting->current_phase === 'draft_open' ? 'selected' : '' }}>🟢 Phase 1 (29th): Draft Roster Entry Open</option>
                            <option value="swap_open" {{ $setting->current_phase === 'swap_open' ? 'selected' : '' }}>🟡 Phase 2 (30th): Merged & Mutual Swap Open</option>
                            <option value="locked_final" {{ $setting->current_phase === 'locked_final' ? 'selected' : '' }}>🔒 Phase 3 (31st): Final Approved & Locked</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Phase 1 Date Label</label>
                        <input type="text" name="phase1_date" value="{{ $setting->phase1_date }}" class="w-full p-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Phase 2 Date Label</label>
                        <input type="text" name="phase2_date" value="{{ $setting->phase2_date }}" class="w-full p-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Phase 3 Date Label</label>
                        <input type="text" name="phase3_date" value="{{ $setting->phase3_date }}" class="w-full p-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1">Higher Authority Approval Note</label>
                        <input type="text" name="approved_by" value="{{ $setting->approved_by }}" placeholder="Approved by Hospital Director" class="w-full p-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-300 mb-1">Top Dashboard Notice Banner Text</label>
                        <input type="text" name="notice_text" value="{{ $setting->notice_text }}" class="w-full p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-amber-200 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Month (1-12)</label>
                            <input type="number" name="active_month" min="1" max="12" value="{{ $setting->active_month }}" class="w-full p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Year</label>
                            <input type="number" name="active_year" value="{{ $setting->active_year }}" class="w-full p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-amber-400 hover:bg-amber-500 text-slate-950 font-black px-6 py-2.5 rounded-xl shadow text-sm">
                        <i class="fa-solid fa-bolt mr-1"></i> Save & Broadcast Settings
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- STEP 1: 7-DAY DRAFT ROSTER SUBMISSION -->
        <div class="bg-white p-6 rounded-2xl shadow-md border-t-4 border-blue-600">
            <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-800">
                        <i class="fa-solid fa-calendar-plus text-blue-600 mr-1"></i>
                        Step 1: My Preferred 7-Day Weekly Duty Roster (8-Hour Shifts)
                    </h2>
                    <p class="text-xs text-slate-500">Once you click Save, your draft will be locked and you will unlock the full hospital roster view.</p>
                </div>

                @if(Auth::user()->role === 'admin')
                <form method="GET" action="{{ route('rosters.index') }}" class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-600">Admin Editing Doctor:</label>
                    <select name="doctor_id" onchange="this.form.submit()" class="border rounded-xl px-3 py-1.5 text-sm font-bold bg-blue-50 text-blue-900">
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ ($myDoctor && $myDoctor->id == $doc->id) ? 'selected' : '' }}>{{ $doc->name }}</option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>

            @php
                $shifts = ['Morning (8AM-4PM)', 'Evening (4PM-12AM)', 'Night (12AM-8AM)', 'Off Day'];
                $days = [
                    'sun_shift' => 'Sunday',
                    'mon_shift' => 'Monday',
                    'tue_shift' => 'Tuesday',
                    'wed_shift' => 'Wednesday',
                    'thu_shift' => 'Thursday',
                    'fri_shift' => 'Friday',
                    'sat_shift' => 'Saturday'
                ];
                $canEditDraft = ($setting->current_phase === 'draft_open' && (!$myRoster || !$myRoster->is_submitted)) || Auth::user()->role === 'admin';
            @endphp

            @if($myDoctor)
                @if($myRoster && $myRoster->is_submitted && Auth::user()->role !== 'admin')
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-sm font-bold mb-4 flex items-center justify-between">
                        <span><i class="fa-solid fa-lock mr-2"></i> Your 7-Day Draft Roster is SUBMITTED & LOCKED. You cannot modify it directly anymore.</span>
                        <span class="bg-blue-700 text-white text-xs px-3 py-1 rounded-full">Locked</span>
                    </div>
                @endif

                <form action="{{ route('rosters.draft.save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $myDoctor->id }}">
                    <div class="grid grid-cols-2 md:grid-cols-7 gap-3 mb-4">
                        @foreach($days as $field => $label)
                        <div class="bg-slate-50 p-3 rounded-xl border">
                            <label class="block text-xs font-extrabold uppercase text-slate-700 mb-2">{{ $label }}</label>
                            <select name="{{ $field }}" {{ !$canEditDraft ? 'disabled' : '' }} class="w-full border p-2 rounded-lg text-xs font-bold bg-white">
                                @foreach($shifts as $s)
                                    <option value="{{ $s }}" {{ ($myRoster && $myRoster->$field === $s) ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                    </div>

                    @if($canEditDraft)
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <input type="text" name="notes" value="{{ $myRoster->notes ?? '' }}" placeholder="Optional preference note..." class="border p-2.5 rounded-xl text-sm flex-1">
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-black px-6 py-2.5 rounded-xl shadow text-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Save & Lock 7-Day Roster
                        </button>
                    </div>
                    @endif
                </form>
            @else
                <div class="p-4 bg-amber-50 text-amber-800 rounded-xl text-sm font-bold">
                    Please add a Doctor in the <a href="{{ route('doctors.create') }}" class="underline">Doctor Directory</a> first!
                </div>
            @endif
        </div>

        <!-- STEP 2: MUTUAL CONFLICT RESOLUTION & SHIFT SWAP REQUISITION (OPEN ON 30TH / SWAP_OPEN) -->
        @if($setting->current_phase === 'swap_open' || Auth::user()->role === 'admin')
        <div class="bg-white p-6 rounded-2xl shadow-md border-t-4 border-amber-500">
            <h2 class="text-xl font-black text-slate-800 mb-1">
                <i class="fa-solid fa-people-arrows text-amber-500 mr-1"></i>
                Step 2: Mutual Shift Swap & Conflict Resolution Requisition (30th Window)
            </h2>
            <p class="text-xs text-slate-500 mb-4">Have a duty conflict? Send a swap request to a colleague. Once accepted, your shifts for that day swap automatically!</p>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Send Requisition Form -->
                <form action="{{ route('rosters.swap.request') }}" method="POST" class="bg-amber-50/70 p-4 rounded-xl border border-amber-200 space-y-3">
                    @csrf
                    <h3 class="font-extrabold text-amber-900 text-sm">Send Swap Requisition</h3>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Requester Doctor (You)</label>
                        <select name="requester_doctor_id" class="w-full border p-2 rounded-lg text-xs font-bold bg-white">
                            @foreach($doctors as $doc)
                                @if(Auth::user()->role === 'admin' || Auth::user()->doctor_id == $doc->id)
                                    <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Swap With Colleague (Target Doctor)</label>
                        <select name="target_doctor_id" class="w-full border p-2 rounded-lg text-xs font-bold bg-white">
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialization ?: 'General' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Select Conflict Day of Week</label>
                        <select name="day_key" class="w-full border p-2 rounded-lg text-xs font-bold bg-white">
                            <option value="sun">Sunday</option>
                            <option value="mon">Monday</option>
                            <option value="tue">Tuesday</option>
                            <option value="wed">Wednesday</option>
                            <option value="thu">Thursday</option>
                            <option value="fri">Friday</option>
                            <option value="sat">Saturday</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Reason / Discussion Note</label>
                        <input type="text" name="reason" placeholder="Mutual agreement over phone..." class="w-full border p-2 rounded-lg text-xs bg-white">
                    </div>
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-black py-2 rounded-lg text-xs shadow">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Send Requisition
                    </button>
                </form>

                <!-- Requisition List & Approval Box -->
                <div class="lg:col-span-2 overflow-x-auto">
                    <h3 class="font-extrabold text-slate-800 text-sm mb-2">Active Swap Requisitions & Approvals</h3>
                    <table class="w-full border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-800 text-white">
                                <th class="p-2.5 text-left">Requester</th>
                                <th class="p-2.5 text-left">Target Doctor</th>
                                <th class="p-2.5 text-center">Day</th>
                                <th class="p-2.5 text-center">Shift Exchange</th>
                                <th class="p-2.5 text-center">Status</th>
                                <th class="p-2.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($swaps as $sw)
                            <tr class="border-b hover:bg-slate-50">
                                <td class="p-2.5 font-bold">{{ $sw->requester->name ?? '' }}</td>
                                <td class="p-2.5 font-bold">{{ $sw->target->name ?? '' }}</td>
                                <td class="p-2.5 text-center uppercase font-black text-blue-700">{{ $sw->day_key }}</td>
                                <td class="p-2.5 text-center">
                                    <span class="px-2 py-0.5 bg-slate-200 rounded">{{ $sw->requester_shift }}</span>
                                    <i class="fa-solid fa-right-left mx-1 text-amber-600"></i>
                                    <span class="px-2 py-0.5 bg-slate-200 rounded">{{ $sw->target_shift }}</span>
                                </td>
                                <td class="p-2.5 text-center">
                                    @if($sw->status === 'accepted')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded-full">Swapped</span>
                                    @elseif($sw->status === 'rejected')
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold rounded-full">Rejected</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold rounded-full">Pending</span>
                                    @endif
                                </td>
                                <td class="p-2.5 text-center">
                                    @if($sw->status === 'pending' && (Auth::user()->role === 'admin' || Auth::user()->doctor_id == $sw->target_doctor_id))
                                    <div class="flex justify-center gap-1">
                                        <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="accept">
                                            <button class="bg-emerald-600 text-white px-2.5 py-1 rounded font-bold hover:bg-emerald-700">Accept</button>
                                        </form>
                                        <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button class="bg-rose-600 text-white px-2.5 py-1 rounded font-bold hover:bg-rose-700">Reject</button>
                                        </form>
                                    </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="p-4 text-center text-slate-400">No shift swap requisitions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- MASTER HOSPITAL ROSTER TABLE (Visible after user submits their draft, or in Phase 2/3, or to Admin) -->
        @php
            $canViewMaster = Auth::user()->role === 'admin'
                || $setting->current_phase !== 'draft_open'
                || ($myRoster && $myRoster->is_submitted);

            function badgeStyle($shift) {
                if (str_contains($shift, 'Morning')) return 'bg-emerald-100 text-emerald-900 border border-emerald-300';
                if (str_contains($shift, 'Evening')) return 'bg-amber-100 text-amber-900 border border-amber-300';
                if (str_contains($shift, 'Night')) return 'bg-indigo-100 text-indigo-900 border border-indigo-300';
                if (str_contains($shift, 'Off')) return 'bg-rose-100 text-rose-800 border border-rose-300';
                return 'bg-slate-100 text-slate-500';
            }
        @endphp

        <div class="bg-white p-6 rounded-2xl shadow-md border">
            <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-800">
                        <i class="fa-solid fa-table-list text-emerald-600 mr-1"></i>
                        Hospital Master 7-Day Merged Roster ({{ $setting->validity_label }})
                    </h2>
                    <p class="text-xs text-slate-500">Read-Only Master View — Nobody can alter another doctor's schedule directly.</p>
                </div>
                @if($setting->current_phase === 'locked_final')
                <div class="bg-emerald-100 border-2 border-emerald-600 text-emerald-900 px-4 py-1.5 rounded-xl text-xs font-black uppercase">
                    <i class="fa-solid fa-stamp mr-1"></i> Verified & Locked: {{ $setting->approved_by }}
                </div>
                @endif
            </div>

            @if($canViewMaster)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase">
                            <th class="p-3 text-left">Doctor Name</th>
                            <th class="p-3 text-center">Sun</th>
                            <th class="p-3 text-center">Mon</th>
                            <th class="p-3 text-center">Tue</th>
                            <th class="p-3 text-center">Wed</th>
                            <th class="p-3 text-center">Thu</th>
                            <th class="p-3 text-center">Fri</th>
                            <th class="p-3 text-center">Sat</th>
                            @if(Auth::user()->role === 'admin')
                            <th class="p-3 text-center">Admin Lock</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doc)
                        @php $r = $doc->weeklyRoster; @endphp
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-800">
                                {{ $doc->name }}
                                <span class="block text-[10px] text-slate-500 font-normal">{{ $doc->specialization ?: 'Medical Officer' }}</span>
                            </td>
                            @foreach(['sun_shift','mon_shift','tue_shift','wed_shift','thu_shift','fri_shift','sat_shift'] as $c)
                            <td class="p-2 text-center">
                                @if($r && $r->is_submitted)
                                    <span class="inline-block px-2 py-1 rounded-lg font-bold text-[11px] {{ badgeStyle($r->$c) }}">
                                        {{ $r->$c }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Pending</span>
                                @endif
                            </td>
                            @endforeach
                            @if(Auth::user()->role === 'admin')
                            <td class="p-2 text-center">
                                @if($r && $r->is_submitted)
                                <form action="{{ route('rosters.draft.unlock', $doc->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-2.5 py-1 rounded font-bold text-[11px]">
                                        <i class="fa-solid fa-unlock mr-1"></i> Unlock
                                    </button>
                                </form>
                                @else
                                    <span class="text-amber-600 font-bold">Open</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 bg-slate-50 rounded-xl border border-dashed text-center text-slate-600 font-bold">
                <i class="fa-solid fa-eye-slash text-2xl text-slate-400 mb-2 block"></i>
                Please submit and lock your own 7-Day Draft Roster above first! Once saved, all other doctors' schedules will appear here automatically.
            </div>
            @endif
        </div>
    </div>
</body>
</html>
