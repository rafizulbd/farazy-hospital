<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Center | {{ $setting->hospital_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">
    @include('partials.nav')

    <main class="max-w-7xl mx-auto w-full px-4 py-6 space-y-6 flex-1">
        @if(session('success')) <div class="p-3 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-lg text-xs font-semibold">{{ session('success') }}</div> @endif
        @if($errors->any()) <div class="p-3 bg-rose-50 border border-rose-300 text-rose-900 rounded-lg text-xs font-semibold">{{ $errors->first() }}</div> @endif

        <!-- Approvals Queue -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white p-5 rounded-xl border">
                <h2 class="text-sm font-bold text-slate-900 mb-3 flex justify-between border-b pb-2">
                    <span>Attendance Correction Requests</span>
                    <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded text-xs">{{ $pendingUpdates->count() }}</span>
                </h2>
                <div class="space-y-2 max-h-56 overflow-y-auto text-xs">
                    @forelse($pendingUpdates as $up)
                    <div class="p-2.5 bg-slate-50 border rounded-lg flex justify-between items-center gap-2">
                        <div>
                            <div class="font-bold">{{ $up->user->name ?? '' }} ({{ $up->duty_date }})</div>
                            <div>Current: {{ $up->in_time ?: '--' }} / {{ $up->out_time ?: '--' }} &rarr; New: <strong>{{ $up->req_in_time }} / {{ $up->req_out_time }}</strong></div>
                            <div class="text-[10px] text-slate-500">Reason: {{ $up->req_reason }}</div>
                        </div>
                        <div class="flex gap-1">
                            <form action="{{ route('attendance.admin.action', $up->id) }}" method="POST">@csrf<input type="hidden" name="action" value="approve_request"><button class="bg-[#065f46] text-white px-2.5 py-1 rounded font-bold">Approve</button></form>
                            <form action="{{ route('attendance.admin.action', $up->id) }}" method="POST">@csrf<input type="hidden" name="action" value="reject_request"><button class="bg-rose-600 text-white px-2.5 py-1 rounded">Reject</button></form>
                        </div>
                    </div>
                    @empty <div class="text-slate-400 text-center py-3">No pending correction requests.</div> @endforelse
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border">
                <h2 class="text-sm font-bold text-slate-900 mb-3 flex justify-between border-b pb-2">
                    <span>Mutual Shift Swap Approvals</span>
                    <span class="bg-purple-100 text-purple-900 px-2 py-0.5 rounded text-xs">{{ $pendingSwaps->count() }}</span>
                </h2>
                <div class="space-y-2 max-h-56 overflow-y-auto text-xs">
                    @forelse($pendingSwaps as $sw)
                    <div class="p-2.5 bg-slate-50 border rounded-lg flex justify-between items-center gap-2">
                        <div>
                            <div class="font-bold">{{ $sw->duty_date }}: {{ $sw->requester->name ?? '' }} &harr; {{ $sw->targetUser->name ?? '' }}</div>
                            <div>{{ $sw->requester_shift }} &harr; {{ $sw->target_shift }} ({{ $sw->status }})</div>
                        </div>
                        <div class="flex gap-1">
                            <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">@csrf<input type="hidden" name="action" value="admin_approve"><button class="bg-purple-700 text-white px-2.5 py-1 rounded font-bold">Approve</button></form>
                            <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">@csrf<input type="hidden" name="action" value="admin_reject"><button class="bg-rose-600 text-white px-2.5 py-1 rounded">Reject</button></form>
                        </div>
                    </div>
                    @empty <div class="text-slate-400 text-center py-3">No pending swap approvals.</div> @endforelse
                </div>
            </div>
        </div>

        <!-- Central Branding, Favicon, Phase & Signatures -->
        <div class="bg-white p-5 rounded-xl border">
            <h2 class="text-base font-bold text-slate-900 mb-3">Central Hospital Branding, Logo, Favicon, Roster Phase & HR Signatures</h2>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div><label class="block font-semibold mb-1">Hospital Name</label><input type="text" name="hospital_name" value="{{ $setting->hospital_name }}" class="w-full border p-2 rounded font-semibold"></div>
                    <div><label class="block font-semibold mb-1">Department</label><input type="text" name="department_name" value="{{ $setting->department_name }}" class="w-full border p-2 rounded font-semibold"></div>
                    <div><label class="block font-semibold mb-1">Address</label><input type="text" name="hospital_address" value="{{ $setting->hospital_address }}" class="w-full border p-2 rounded"></div>
                    <div><label class="block font-semibold mb-1">Logo URL</label><input type="text" name="logo_url" value="{{ $setting->logo_url }}" class="w-full border p-2 rounded"></div>
                    <div><label class="block font-semibold mb-1">Favicon URL</label><input type="text" name="favicon_url" value="{{ $setting->favicon_url }}" class="w-full border p-2 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Active Roster Phase</label>
                        <select name="roster_phase" class="w-full border p-2 rounded font-bold bg-emerald-50">
                            <option value="draft_open" {{ $setting->roster_phase === 'draft_open' ? 'selected' : '' }}>29th: Draft Open</option>
                            <option value="merge_review" {{ $setting->roster_phase === 'merge_review' ? 'selected' : '' }}>30th: Merged & Swap Open</option>
                            <option value="final_verified" {{ $setting->roster_phase === 'final_verified' ? 'selected' : '' }}>31st: Final Verified</option>
                            <option value="closed" {{ $setting->roster_phase === 'closed' ? 'selected' : '' }}>Closed / Locked</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="block font-semibold mb-1">Target Month</label><input type="number" min="1" max="12" name="target_month" value="{{ $setting->target_month }}" class="w-full border p-2 rounded"></div>
                        <div><label class="block font-semibold mb-1">Target Year</label><input type="number" name="target_year" value="{{ $setting->target_year }}" class="w-full border p-2 rounded"></div>
                    </div>
                    <div><label class="block font-semibold mb-1">Validity Label</label><input type="text" name="validity_text" value="{{ $setting->validity_text }}" class="w-full border p-2 rounded"></div>
                    <div><label class="block font-semibold mb-1">Verified By</label><input type="text" name="verified_by" value="{{ $setting->verified_by }}" class="w-full border p-2 rounded"></div>
                </div>
                <div><label class="block font-semibold mb-1">Top Notice Banner</label><input type="text" name="notice_text" value="{{ $setting->notice_text }}" class="w-full border p-2 rounded"></div>
                <div class="flex flex-wrap gap-6 bg-slate-50 p-2.5 rounded border">
                    <label class="inline-flex items-center gap-2 font-bold"><input type="checkbox" name="is_verified" value="1" {{ $setting->is_verified ? 'checked' : '' }}> <span>Show Official Verified Tick on Dashboards</span></label>
                    <label class="inline-flex items-center gap-2 font-bold text-amber-900"><input type="checkbox" name="unlock_past_attendance" value="1" {{ $setting->unlock_past_attendance ? 'checked' : '' }}> <span>Override 2nd-of-Month Midnight Lock</span></label>
                </div>
                <div class="border-t pt-3 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div><input type="text" name="sig1_name" value="{{ $setting->sig1_name }}" class="w-full border p-1.5 rounded font-bold mb-1"><input type="text" name="sig1_title" value="{{ $setting->sig1_title }}" class="w-full border p-1.5 rounded text-[11px]"></div>
                    <div><input type="text" name="sig2_name" value="{{ $setting->sig2_name }}" class="w-full border p-1.5 rounded font-bold mb-1"><input type="text" name="sig2_title" value="{{ $setting->sig2_title }}" class="w-full border p-1.5 rounded text-[11px]"></div>
                    <div><input type="text" name="sig3_name" value="{{ $setting->sig3_name }}" class="w-full border p-1.5 rounded font-bold mb-1"><input type="text" name="sig3_title" value="{{ $setting->sig3_title }}" class="w-full border p-1.5 rounded text-[11px]"></div>
                    <div><input type="text" name="sig4_name" value="{{ $setting->sig4_name }}" class="w-full border p-1.5 rounded font-bold mb-1"><input type="text" name="sig4_title" value="{{ $setting->sig4_title }}" class="w-full border p-1.5 rounded text-[11px]"></div>
                </div>
                <button type="submit" class="bg-[#065f46] text-white font-bold px-5 py-2 rounded">Save Central Configuration</button>
            </form>
        </div>

        <!-- Super Admin User Manager -->
        <div class="bg-white p-5 rounded-xl border">
            <h2 class="text-base font-bold text-slate-900 mb-3">Staff Directory & Super Access User Manager</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="bg-slate-50 p-3.5 rounded-lg border mb-4 space-y-2.5 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-6 gap-2">
                    <input type="text" name="name" placeholder="Full Name *" class="border p-2 rounded bg-white" required>
                    <input type="text" name="employee_id" placeholder="ID (FH-FD-004)" class="border p-2 rounded bg-white">
                    <input type="text" name="designation" value="Front Desk Executive" class="border p-2 rounded bg-white">
                    <input type="email" name="email" placeholder="Official Email *" class="border p-2 rounded bg-white" required>
                    <input type="text" name="whatsapp_number" placeholder="WhatsApp" class="border p-2 rounded bg-white">
                    <input type="text" name="password" value="password123" class="border p-2 rounded bg-white">
                </div>
                <div class="flex flex-wrap justify-between items-center gap-2">
                    <div class="flex flex-wrap gap-1.5 items-center">
                        <span class="font-bold">Allowed Shifts:</span>
                        @foreach($shifts as $sh)
                            <label class="inline-flex items-center gap-1 bg-white px-2 py-0.5 rounded border text-[11px]"><input type="checkbox" name="allowed_shifts[]" value="{{ $sh->name }}" checked> {{ $sh->name }}</label>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <select name="role" class="border p-1.5 rounded bg-white font-semibold"><option value="staff">Role: Staff</option><option value="admin">Role: Super Admin</option></select>
                        <button type="submit" class="bg-[#065f46] text-white font-bold px-4 py-1.5 rounded">+ Create User</button>
                    </div>
                </div>
            </form>

            <table class="w-full text-xs border">
                <thead><tr class="bg-slate-800 text-white"><th class="p-2 text-left">Name & ID</th><th class="p-2 text-left">Designation</th><th class="p-2 text-left">Email & WhatsApp</th><th class="p-2 text-center">Role</th><th class="p-2 text-center">Actions</th></tr></thead>
                <tbody>
                    @foreach($users as $u)
                    <tr class="border-b">
                        <td class="p-2 font-bold">{{ $u->name }} <span class="text-[10px] text-emerald-700 block">{{ $u->employee_id }}</span></td>
                        <td class="p-2">{{ $u->designation }}</td>
                        <td class="p-2">{{ $u->email }} <span class="text-[10px] text-slate-500 block">WA: {{ $u->whatsapp_number ?: '-' }}</span></td>
                        <td class="p-2 text-center font-bold uppercase">{{ $u->role }}</td>
                        <td class="p-2 text-center">
                            <div class="flex justify-center gap-1.5">
                                <a href="{{ route('rosters.index', ['user_id' => $u->id]) }}" class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-2 py-1 rounded font-semibold">Roster</a>
                                <a href="{{ route('report.print', ['user_id' => $u->id]) }}" class="bg-slate-100 border px-2 py-1 rounded font-semibold">Print A4</a>
                                <details class="inline-block text-left">
                                    <summary class="cursor-pointer bg-amber-50 border border-amber-300 text-amber-900 px-2 py-1 rounded font-semibold">Edit</summary>
                                    <form action="{{ route('admin.users.update', $u->id) }}" method="POST" class="mt-1 p-2.5 bg-white border rounded-lg shadow-xl space-y-1.5 w-60 absolute right-8 z-30">
                                        @csrf
                                        <input type="text" name="name" value="{{ $u->name }}" class="w-full border p-1 rounded text-xs" placeholder="Name">
                                        <input type="text" name="employee_id" value="{{ $u->employee_id }}" class="w-full border p-1 rounded text-xs" placeholder="ID">
                                        <input type="text" name="designation" value="{{ $u->designation }}" class="w-full border p-1 rounded text-xs" placeholder="Designation">
                                        <input type="email" name="email" value="{{ $u->email }}" class="w-full border p-1 rounded text-xs" placeholder="Email">
                                        <input type="text" name="whatsapp_number" value="{{ $u->whatsapp_number }}" class="w-full border p-1 rounded text-xs" placeholder="WhatsApp">
                                        <select name="role" class="w-full border p-1 rounded text-xs"><option value="staff" {{ $u->role==='staff'?'selected':'' }}>Staff</option><option value="admin" {{ $u->role==='admin'?'selected':'' }}>Admin</option></select>
                                        <input type="text" name="new_password" placeholder="New Password (optional)" class="w-full border p-1 rounded text-xs">
                                        <button type="submit" class="w-full bg-[#065f46] text-white py-1 rounded text-xs font-bold">Update User</button>
                                    </form>
                                </details>
                                @if($u->id !== Auth::id())
                                <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete user?');">
                                    @csrf <button class="bg-rose-50 border border-rose-200 text-rose-700 px-2 py-1 rounded font-bold">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Custom Shift Builder & Audit Logs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white p-5 rounded-xl border">
                <h2 class="text-sm font-bold text-slate-900 mb-3">Custom Shift Templates & Grace Rules</h2>
                <form action="{{ route('admin.shifts.store') }}" method="POST" class="bg-slate-50 p-3 rounded border mb-3 space-y-2 text-xs">
                    @csrf
                    <input type="text" name="name" placeholder="Shift Name (e.g. Morning 7AM - 3PM)" class="w-full border p-1.5 rounded bg-white" required>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="time" name="start_time" class="border p-1.5 rounded bg-white" title="Start Time">
                        <input type="time" name="end_time" class="border p-1.5 rounded bg-white" title="End Time">
                        <input type="number" name="grace_minutes" value="5" class="border p-1.5 rounded bg-white" placeholder="Grace Mins">
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="inline-flex items-center gap-1 font-semibold"><input type="checkbox" name="is_off_day" value="1"> Off Day</label>
                        <button type="submit" class="bg-slate-800 text-white font-bold px-3 py-1 rounded">+ Add Shift</button>
                    </div>
                </form>
                <table class="w-full text-xs border">
                    <thead><tr class="bg-slate-800 text-white"><th class="p-1.5 text-left">Shift</th><th>Start</th><th>End</th><th>Grace</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($shifts as $s)
                        <tr class="border-b"><td class="p-1.5 font-semibold">{{ $s->name }}</td><td class="text-center">{{ $s->start_time ?: '-' }}</td><td class="text-center">{{ $s->end_time ?: '-' }}</td><td class="text-center">{{ $s->grace_minutes }}m</td><td class="text-center"><form action="{{ route('admin.shifts.delete', $s->id) }}" method="POST">@csrf<button class="text-rose-600 font-bold">Del</button></form></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-5 rounded-xl border">
                <h2 class="text-sm font-bold text-slate-900 mb-3">Audit & Activity History Log</h2>
                <div class="max-h-64 overflow-y-auto border rounded">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-800 text-white sticky top-0"><tr><th class="p-1.5 text-left">Time</th><th class="p-1.5 text-left">User</th><th class="p-1.5 text-left">Details</th></tr></thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr class="border-b"><td class="p-1.5 text-[10px] text-slate-500">{{ $log->created_at->format('d M, h:i A') }}</td><td class="p-1.5 font-semibold">{{ $log->user_name }}</td><td class="p-1.5">{{ $log->description }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
