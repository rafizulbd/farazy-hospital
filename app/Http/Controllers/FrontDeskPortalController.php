<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\ShiftTemplate;
use App\Models\WeeklyRoster;
use App\Models\DailyRoster;
use App\Models\ShiftSwap;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class FrontDeskPortalController extends Controller
{
    private function getSetting() {
        return SystemSetting::first() ?? SystemSetting::create(['hospital_name' => 'Farazy Hospital Ltd.']);
    }

    public static function isDateLockedForAttendance($dutyDate, $setting) {
        if ($setting->unlock_past_attendance) return false;
        $date = Carbon::parse($dutyDate);
        $now = Carbon::now();
        if ($date->year < $now->year || ($date->year == $now->year && $date->month < $now->month)) {
            $deadline = Carbon::create($date->year, $date->month, 1)->addMonth()->day(2)->endOfDay();
            if ($now->greaterThan($deadline)) return true;
        }
        return false;
    }

    private function getShiftsForUser($user) {
        $all = ShiftTemplate::all();
        if (!empty($user->allowed_shifts) && is_array($user->allowed_shifts)) {
            $filtered = $all->whereIn('name', $user->allowed_shifts);
            if ($filtered->count() > 0) return $filtered;
        }
        return $all;
    }

    public function dashboard(Request $request) {
        $setting = $this->getSetting();
        $user = Auth::user();
        $team = User::where('is_active', true)->orderBy('name')->get();
        $viewUserId = $request->filled('user_id') ? (int)$request->query('user_id') : $user->id;
        $activeUser = User::find($viewUserId) ?? $user;

        $month = (int)$request->query('month', $setting->target_month ?: date('n'));
        $year = (int)$request->query('year', $setting->target_year ?: date('Y'));

        $this->ensureMonthlyDaysExist($activeUser, $month, $year);
        $this->ensureMonthlyDaysExist($activeUser, (int)date('n'), (int)date('Y'));

        $twoDaysAgoDuty = DailyRoster::where('user_id', $activeUser->id)->where('duty_date', Carbon::today()->subDays(2)->toDateString())->first();
        $yesterdayDuty  = DailyRoster::where('user_id', $activeUser->id)->where('duty_date', Carbon::today()->subDay()->toDateString())->first();
        $todayDuty      = DailyRoster::where('user_id', $activeUser->id)->where('duty_date', Carbon::today()->toDateString())->first();
        $tomorrowDuty   = DailyRoster::where('user_id', $activeUser->id)->where('duty_date', Carbon::today()->addDay()->toDateString())->first();

        $monthlyDuties = DailyRoster::where('user_id', $activeUser->id)->whereYear('duty_date', $year)->whereMonth('duty_date', $month)->orderBy('duty_date')->get()->keyBy('duty_date');

        $kpi = [
            'regular_days' => $monthlyDuties->where('is_off_day', false)->count(),
            'present_days' => $monthlyDuties->whereIn('attendance_status', ['Present', 'Late'])->count(),
            'late_days'    => $monthlyDuties->where('attendance_status', 'Late')->count(),
            'off_days'     => $monthlyDuties->where('is_off_day', true)->count(),
            'ot_hours'     => $monthlyDuties->where('has_overtime', true)->sum('overtime_hours'),
            'swapped_days' => $monthlyDuties->where('is_swapped', true)->count(),
        ];

        $shifts = ShiftTemplate::all();
        $pendingSwapsCount = ShiftSwap::where('target_user_id', $user->id)->where('status', 'pending_colleague')->count();
        $adminPendingSwaps = ShiftSwap::where('status', 'colleague_accepted')->count();
        $adminPendingUpdates = DailyRoster::where('update_status', 'pending')->count();

        return view('dashboard', compact('setting', 'user', 'team', 'activeUser', 'month', 'year', 'twoDaysAgoDuty', 'yesterdayDuty', 'todayDuty', 'tomorrowDuty', 'monthlyDuties', 'kpi', 'shifts', 'pendingSwapsCount', 'adminPendingSwaps', 'adminPendingUpdates'));
    }

    private function ensureMonthlyDaysExist($user, $month, $year) {
        if (DailyRoster::where('user_id', $user->id)->whereYear('duty_date', $year)->whereMonth('duty_date', $month)->count() > 0) return;
        $weekly = WeeklyRoster::where('user_id', $user->id)->where('month', $month)->where('year', $year)->first() ?? WeeklyRoster::where('user_id', $user->id)->latest()->first();
        if ($weekly) $this->generateDailyFromWeekly($user, $weekly, $month, $year);
    }

    private function generateDailyFromWeekly($user, $weekly, $month, $year) {
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $shiftMap = ShiftTemplate::all()->keyBy('name');
        $dayColMap = ['Sun'=>'sun_shift','Mon'=>'mon_shift','Tue'=>'tue_shift','Wed'=>'wed_shift','Thu'=>'thu_shift','Fri'=>'fri_shift','Sat'=>'sat_shift'];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateObj = Carbon::create($year, $month, $d);
            $shiftName = $weekly->{$dayColMap[$dateObj->format('D')]};
            $tpl = $shiftMap->get($shiftName);
            $isOff = $tpl ? $tpl->is_off_day : str_contains(strtolower($shiftName), 'off');

            DailyRoster::updateOrCreate(
                ['user_id' => $user->id, 'duty_date' => $dateObj->toDateString()],
                [
                    'day_name' => $dateObj->format('l'),
                    'shift_name' => $shiftName,
                    'shift_start' => $tpl ? $tpl->start_time : null,
                    'shift_end' => $tpl ? $tpl->end_time : null,
                    'is_off_day' => $isOff,
                    'attendance_status' => $isOff ? 'Off Day' : 'Pending',
                ]
            );
        }
    }

    public function rosterPortal(Request $request) {
        $setting = $this->getSetting();
        $user = Auth::user();
        $team = User::where('is_active', true)->orderBy('name')->get();
        $month = (int)$request->query('month', $setting->target_month);
        $year = (int)$request->query('year', $setting->target_year);

        $selectedUserId = ($user->role === 'admin' && $request->filled('user_id')) ? (int)$request->query('user_id') : $user->id;
        $targetUser = User::find($selectedUserId) ?? $user;

        $allowedShifts = $this->getShiftsForUser($targetUser);
        $allShifts = ShiftTemplate::all();
        $myDraft = WeeklyRoster::where('user_id', $targetUser->id)->where('month', $month)->where('year', $year)->first();
        $allDrafts = WeeklyRoster::with('user')->where('month', $month)->where('year', $year)->get()->keyBy('user_id');

        $submittedUsers = [];
        $pendingUsers = [];
        foreach ($team as $member) {
            if (isset($allDrafts[$member->id]) && $allDrafts[$member->id]->is_submitted) {
                $submittedUsers[] = $member;
            } else {
                $pendingUsers[] = $member;
            }
        }

        $swaps = ShiftSwap::with(['requester', 'targetUser'])->latest()->get();
        $this->ensureMonthlyDaysExist($targetUser, $month, $year);
        $myMonthlyDuties = DailyRoster::where('user_id', $targetUser->id)->whereYear('duty_date', $year)->whereMonth('duty_date', $month)->orderBy('duty_date')->get();

        return view('rosters.index', compact('setting', 'user', 'team', 'targetUser', 'month', 'year', 'allowedShifts', 'allShifts', 'myDraft', 'allDrafts', 'submittedUsers', 'pendingUsers', 'swaps', 'myMonthlyDuties'));
    }

    public function saveDraft(Request $request) {
        $setting = $this->getSetting();
        $user = Auth::user();
        if ($setting->roster_phase !== 'draft_open' && $user->role !== 'admin') {
            return back()->withErrors(['msg' => 'Draft Roster window is currently closed!']);
        }
        $targetUserId = ($user->role === 'admin' && $request->filled('user_id')) ? (int)$request->user_id : $user->id;
        $month = (int)$request->input('month', $setting->target_month);
        $year  = (int)$request->input('year', $setting->target_year);

        $existing = WeeklyRoster::where('user_id', $targetUserId)->where('month', $month)->where('year', $year)->first();
        if ($existing && $existing->is_submitted && $user->role !== 'admin') {
            return back()->withErrors(['msg' => 'Your draft roster is already locked!']);
        }

        $days = ['sun_shift', 'mon_shift', 'tue_shift', 'wed_shift', 'thu_shift', 'fri_shift', 'sat_shift'];
        $offCount = 0;
        foreach ($days as $d) {
            if (str_contains(strtolower((string)$request->input($d)), 'off')) $offCount++;
        }
        if ($offCount !== 1 && $user->role !== 'admin') {
            return back()->withErrors(['msg' => 'Roster must have 6 Days Duty and exactly 1 Off Day!']);
        }

        $draft = WeeklyRoster::updateOrCreate(
            ['user_id' => $targetUserId, 'month' => $month, 'year' => $year],
            [
                'sun_shift' => $request->sun_shift, 'mon_shift' => $request->mon_shift,
                'tue_shift' => $request->tue_shift, 'wed_shift' => $request->wed_shift,
                'thu_shift' => $request->thu_shift, 'fri_shift' => $request->fri_shift,
                'sat_shift' => $request->sat_shift, 'notes' => $request->notes, 'is_submitted' => true,
            ]
        );

        $tUser = User::find($targetUserId);
        $this->generateDailyFromWeekly($tUser, $draft, $month, $year);
        ActivityLog::record('DRAFT_SUBMITTED', "Saved 7-day draft roster for {$tUser->name}.");
        return back()->with('success', '7-Day Draft Roster (6 Days Duty + 1 Day Off) locked successfully!');
    }

    public function mergeAndGenerateMonth(Request $request) {
        if (Auth::user()->role !== 'admin') abort(403);
        $setting = $this->getSetting();
        $month = (int)$request->input('month', $setting->target_month);
        $year  = (int)$request->input('year', $setting->target_year);

        foreach (WeeklyRoster::with('user')->where('month', $month)->where('year', $year)->get() as $draft) {
            if ($draft->user) $this->generateDailyFromWeekly($draft->user, $draft, $month, $year);
        }

        $setting->roster_phase = $request->input('next_phase', 'merge_review');
        if ($setting->roster_phase === 'final_verified') {
            $setting->is_verified = true;
            $setting->verified_by = $request->input('verified_by', Auth::user()->name);
        }
        $setting->save();
        ActivityLog::record('ROSTER_MERGED', "Merged roster for {$month}/{$year}.");
        return back()->with('success', 'Monthly Roster Merged & Phase Updated!');
    }

    public function requestSwap(Request $request) {
        $user = Auth::user();
        $request->validate(['target_user_id' => 'required|exists:users,id', 'duty_date' => 'required|date']);
        $requesterId = ($user->role === 'admin' && $request->filled('requester_id')) ? (int)$request->requester_id : $user->id;
        if ($requesterId == $request->target_user_id) return back()->withErrors(['msg' => 'Cannot swap with yourself!']);

        $myDuty = DailyRoster::where('user_id', $requesterId)->where('duty_date', $request->duty_date)->first();
        $tarDuty = DailyRoster::where('user_id', $request->target_user_id)->where('duty_date', $request->duty_date)->first();
        if (!$myDuty || !$tarDuty) return back()->withErrors(['msg' => 'Both staff must have assigned rosters on that date!']);

        ShiftSwap::create([
            'requester_id' => $requesterId, 'target_user_id' => $request->target_user_id,
            'duty_date' => $request->duty_date, 'requester_shift' => $myDuty->shift_name,
            'target_shift' => $tarDuty->shift_name, 'reason' => $request->reason, 'status' => 'pending_colleague',
        ]);
        return back()->with('success', 'Shift Swap request sent to colleague!');
    }

    public function respondSwap(Request $request, $id) {
        $swap = ShiftSwap::with(['requester', 'targetUser'])->findOrFail($id);
        $user = Auth::user();
        $action = $request->input('action');

        if ($swap->status === 'pending_colleague' && ($user->id == $swap->target_user_id || $user->role === 'admin')) {
            if ($action === 'colleague_accept') {
                $swap->status = 'colleague_accepted';
                $swap->save();
                return back()->with('success', 'Accepted! Waiting for Admin Approval.');
            } else {
                $swap->status = 'colleague_rejected';
                $swap->save();
                return back()->with('success', 'Swap rejected.');
            }
        }

        if ($user->role === 'admin') {
            if ($action === 'admin_approve') {
                $reqDuty = DailyRoster::where('user_id', $swap->requester_id)->where('duty_date', $swap->duty_date)->first();
                $tarDuty = DailyRoster::where('user_id', $swap->target_user_id)->where('duty_date', $swap->duty_date)->first();
                if ($reqDuty && $tarDuty) {
                    $tName = $reqDuty->shift_name; $tStart = $reqDuty->shift_start; $tEnd = $reqDuty->shift_end; $tOff = $reqDuty->is_off_day;
                    $reqDuty->update(['shift_name' => $tarDuty->shift_name, 'shift_start' => $tarDuty->shift_start, 'shift_end' => $tarDuty->shift_end, 'is_off_day' => $tarDuty->is_off_day, 'is_swapped' => true, 'swap_info' => 'Swapped with ' . $swap->targetUser->name]);
                    $tarDuty->update(['shift_name' => $tName, 'shift_start' => $tStart, 'shift_end' => $tEnd, 'is_off_day' => $tOff, 'is_swapped' => true, 'swap_info' => 'Swapped with ' . $swap->requester->name]);
                }
                $swap->update(['status' => 'admin_approved']);
                return back()->with('success', 'Admin approved! Main roster updated.');
            } else {
                $swap->update(['status' => 'admin_rejected']);
                return back()->with('success', 'Swap rejected by Admin.');
            }
        }
        return back();
    }

    public function punchAttendance(Request $request, $id) {
        $duty = DailyRoster::with('user')->findOrFail($id);
        $user = Auth::user();
        $setting = $this->getSetting();

        if ($user->id !== $duty->user_id && $user->role !== 'admin') abort(403);
        if (self::isDateLockedForAttendance($duty->duty_date, $setting) && $user->role !== 'admin') {
            return back()->withErrors(['msg' => 'Locked past 2nd of month midnight!']);
        }

        $type = $request->input('punch_type');
        if ($type === 'in_time') {
            if ($duty->in_locked && $user->role !== 'admin') return back()->withErrors(['msg' => 'In-Time is already locked!']);
            $duty->in_time = $request->in_time;
            $duty->in_locked = true;
            $this->recalculateLateStatus($duty);
            $duty->save();
            return back()->with('success', "In-Time ({$duty->in_time}) saved & locked!");
        }
        if ($type === 'out_time') {
            if ($duty->out_locked && $user->role !== 'admin') return back()->withErrors(['msg' => 'Out-Time is already locked!']);
            $duty->out_time = $request->out_time;
            $duty->out_locked = true;
            $duty->save();
            return back()->with('success', "Out-Time ({$duty->out_time}) saved & locked!");
        }
        if ($type === 'overtime') {
            $duty->has_overtime = $request->filled('overtime_shift');
            $duty->overtime_shift = $request->overtime_shift;
            $duty->overtime_hours = (float)$request->input('overtime_hours', 8);
            $duty->save();
            return back()->with('success', 'Overtime shift saved!');
        }
        return back();
    }

    private function recalculateLateStatus(DailyRoster $duty) {
        if ($duty->is_off_day) { $duty->attendance_status = 'Off Day'; $duty->late_minutes = 0; return; }
        if (empty($duty->in_time)) return;
        if (!empty($duty->shift_start)) {
            $tpl = ShiftTemplate::where('name', $duty->shift_name)->first();
            $grace = $tpl ? (int)$tpl->grace_minutes : 0;
            $diff = Carbon::parse($duty->duty_date . ' ' . $duty->shift_start)->diffInMinutes(Carbon::parse($duty->duty_date . ' ' . $duty->in_time), false);
            if ($diff > $grace) {
                $duty->attendance_status = 'Late';
                $duty->late_minutes = $diff;
            } else {
                $duty->attendance_status = 'Present';
                $duty->late_minutes = 0;
            }
        } else {
            $duty->attendance_status = 'Present';
        }
    }

    public function requestAttendanceUpdate(Request $request, $id) {
        $duty = DailyRoster::findOrFail($id);
        $duty->update([
            'req_in_time' => $request->req_in_time ?: $duty->in_time,
            'req_out_time' => $request->req_out_time ?: $duty->out_time,
            'req_overtime_shift' => $request->req_overtime_shift,
            'req_overtime_hours' => (float)$request->input('req_overtime_hours', $duty->overtime_hours),
            'req_reason' => $request->req_reason,
            'update_status' => 'pending',
        ]);
        return back()->with('success', 'Update Request submitted for approval!');
    }

    public function adminApproveAttendance(Request $request, $id) {
        if (Auth::user()->role !== 'admin') abort(403);
        $duty = DailyRoster::findOrFail($id);
        $action = $request->input('action');

        if ($action === 'approve_request') {
            if ($duty->req_in_time) { $duty->in_time = $duty->req_in_time; $duty->in_locked = true; }
            if ($duty->req_out_time) { $duty->out_time = $duty->req_out_time; $duty->out_locked = true; }
            if ($duty->req_overtime_shift !== null) {
                $duty->has_overtime = !empty($duty->req_overtime_shift);
                $duty->overtime_shift = $duty->req_overtime_shift;
                $duty->overtime_hours = (float)$duty->req_overtime_hours;
            }
            $this->recalculateLateStatus($duty);
            $duty->update_status = 'approved';
            $duty->save();
            return back()->with('success', 'Attendance update approved!');
        }

        if ($action === 'direct_override') {
            $duty->shift_name = $request->input('shift_name', $duty->shift_name);
            $tpl = ShiftTemplate::where('name', $duty->shift_name)->first();
            $duty->is_off_day = $tpl ? $tpl->is_off_day : str_contains(strtolower($duty->shift_name), 'off');
            $duty->in_time = $request->in_time;
            $duty->out_time = $request->out_time;
            $duty->in_locked = !empty($request->in_time);
            $duty->out_locked = !empty($request->out_time);
            $duty->has_overtime = $request->filled('overtime_shift');
            $duty->overtime_shift = $request->overtime_shift;
            $duty->overtime_hours = (float)$request->input('overtime_hours', 0);
            $duty->remarks = $request->remarks;

            if ($request->filled('attendance_status') && $request->attendance_status !== 'Auto') {
                $duty->attendance_status = $request->attendance_status;
            } else {
                $this->recalculateLateStatus($duty);
            }
            $duty->update_status = 'approved';
            $duty->save();
            return back()->with('success', 'Super Admin Override saved!');
        }

        if ($action === 'reject_request') {
            $duty->update(['update_status' => 'rejected']);
            return back()->with('success', 'Request rejected.');
        }
        return back();
    }

    public function printReport(Request $request) {
        $setting = $this->getSetting();
        $user = Auth::user();
        $team = User::where('is_active', true)->orderBy('name')->get();
        $targetUserId = $request->filled('user_id') ? (int)$request->query('user_id') : $user->id;
        $reportUser = User::find($targetUserId) ?? $user;

        $filterMode = $request->query('mode', 'monthly');
        $month = (int)$request->query('month', $setting->target_month ?: date('n'));
        $year  = (int)$request->query('year', $setting->target_year ?: date('Y'));
        $this->ensureMonthlyDaysExist($reportUser, $month, $year);

        $query = DailyRoster::where('user_id', $reportUser->id)->orderBy('duty_date');
        if ($filterMode === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('duty_date', [$request->start_date, $request->end_date]);
            $periodLabel = Carbon::parse($request->start_date)->format('d M Y') . ' to ' . Carbon::parse($request->end_date)->format('d M Y');
        } elseif ($filterMode === 'daily' && $request->filled('single_date')) {
            $query->where('duty_date', $request->single_date);
            $periodLabel = Carbon::parse($request->single_date)->format('d F Y (l)');
        } else {
            $query->whereYear('duty_date', $year)->whereMonth('duty_date', $month);
            $periodLabel = Carbon::create($year, $month, 1)->format('F Y');
        }

        $records = $query->get();
        $summary = [
            'regular_duty' => $records->where('is_off_day', false)->whereIn('attendance_status', ['Present', 'Late', 'Pending'])->count(),
            'present_count' => $records->whereIn('attendance_status', ['Present', 'Late'])->count(),
            'off_days'     => $records->where('is_off_day', true)->count(),
            'late_count'   => $records->where('attendance_status', 'Late')->count(),
            'absent_count' => $records->where('attendance_status', 'Absent')->count(),
            'ot_count'     => $records->where('has_overtime', true)->count(),
            'ot_hours'     => $records->where('has_overtime', true)->sum('overtime_hours'),
            'swapped_count'=> $records->where('is_swapped', true)->count(),
        ];

        return view('rosters.print-a4', compact('setting', 'user', 'team', 'reportUser', 'records', 'summary', 'filterMode', 'month', 'year', 'periodLabel'));
    }

    public function exportCsv(Request $request) {
        $setting = $this->getSetting();
        $targetUserId = $request->filled('user_id') ? (int)$request->query('user_id') : Auth::id();
        $reportUser = User::find($targetUserId) ?? Auth::user();
        $month = (int)$request->query('month', $setting->target_month ?: date('n'));
        $year  = (int)$request->query('year', $setting->target_year ?: date('Y'));
        $records = DailyRoster::where('user_id', $reportUser->id)->whereYear('duty_date', $year)->whereMonth('duty_date', $month)->orderBy('duty_date')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"Roster_{$reportUser->employee_id}_{$month}_{$year}.csv\""];
        $callback = function() use ($records, $reportUser, $setting) {
            $f = fopen('php://output', 'w');
            fputcsv($f, [$setting->hospital_name, $setting->department_name]);
            fputcsv($f, ['Staff:', $reportUser->name, 'ID:', $reportUser->employee_id, 'Designation:', $reportUser->designation]);
            fputcsv($f, []);
            fputcsv($f, ['Date', 'Day', 'Shift', 'In-Time', 'Out-Time', 'OT Shift', 'OT Hours', 'Status', 'Late (Mins)', 'Remarks']);
            foreach ($records as $r) {
                fputcsv($f, [$r->duty_date, $r->day_name, $r->shift_name, $r->in_time ?: '-', $r->out_time ?: '-', $r->overtime_shift ?: '-', $r->overtime_hours, $r->attendance_status, $r->late_minutes, $r->remarks ?: '-']);
            }
            fclose($f);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function adminPanel(Request $request) {
        if (Auth::user()->role !== 'admin') abort(403);
        $setting = $this->getSetting();
        $users = User::orderBy('role')->orderBy('name')->get();
        $shifts = ShiftTemplate::all();
        $pendingSwaps = ShiftSwap::with(['requester', 'targetUser'])->whereIn('status', ['colleague_accepted', 'pending_colleague'])->latest()->get();
        $pendingUpdates = DailyRoster::with('user')->where('update_status', 'pending')->orderBy('duty_date', 'desc')->get();
        $logs = ActivityLog::latest()->limit(100)->get();

        return view('admin.panel', compact('setting', 'users', 'shifts', 'pendingSwaps', 'pendingUpdates', 'logs'));
    }

    public function updateSystemSettings(Request $request) {
        if (Auth::user()->role !== 'admin') abort(403);
        $this->getSetting()->update([
            'hospital_name' => $request->hospital_name,
            'hospital_address' => $request->hospital_address,
            'department_name' => $request->department_name,
            'logo_url' => $request->logo_url,
            'favicon_url' => $request->favicon_url,
            'notice_text' => $request->notice_text,
            'roster_phase' => $request->roster_phase,
            'target_month' => (int)$request->target_month,
            'target_year' => (int)$request->target_year,
            'validity_text' => $request->validity_text,
            'is_verified' => $request->has('is_verified'),
            'verified_by' => $request->verified_by,
            'unlock_past_attendance' => $request->has('unlock_past_attendance'),
            'sig1_name' => $request->sig1_name, 'sig1_title' => $request->sig1_title,
            'sig2_name' => $request->sig2_name, 'sig2_title' => $request->sig2_title,
            'sig3_name' => $request->sig3_name, 'sig3_title' => $request->sig3_title,
            'sig4_name' => $request->sig4_name, 'sig4_title' => $request->sig4_title,
        ]);
        return back()->with('success', 'Central configuration updated successfully!');
    }

    public function storeUser(Request $request) {
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users,email']);
        User::create([
            'name' => $request->name, 'email' => $request->email,
            'password' => Hash::make($request->password ?: 'password123'),
            'whatsapp_number' => $request->whatsapp_number,
            'employee_id' => $request->employee_id ?: 'FH-FD-' . rand(100, 999),
            'designation' => $request->designation ?: 'Front Desk Executive',
            'department' => $request->department ?: 'Front Desk Department',
            'role' => $request->role ?: 'staff',
            'allowed_shifts' => $request->allowed_shifts ?? [],
            'is_password_changed' => false, 'is_active' => true,
        ]);
        return back()->with('success', 'User created successfully!');
    }

    public function updateUser(Request $request, $id) {
        if (Auth::user()->role !== 'admin') abort(403);
        $u = User::findOrFail($id);
        $u->name = $request->name ?: $u->name;
        $u->email = $request->email ?: $u->email;
        $u->whatsapp_number = $request->whatsapp_number;
        $u->employee_id = $request->employee_id ?: $u->employee_id;
        $u->designation = $request->designation ?: $u->designation;
        $u->role = $request->role ?: $u->role;
        if ($request->has('allowed_shifts')) $u->allowed_shifts = $request->allowed_shifts;
        if ($request->filled('new_password')) {
            $u->password = Hash::make($request->new_password);
            $u->is_password_changed = false;
        }
        $u->save();
        return back()->with('success', 'User profile updated!');
    }

    public function deleteUser($id) {
        if (Auth::user()->role !== 'admin') abort(403);
        $u = User::findOrFail($id);
        if ($u->id === Auth::id()) return back()->withErrors(['msg' => 'Cannot delete your own admin account!']);
        $u->delete();
        return back()->with('success', 'User deleted.');
    }

    public function storeShift(Request $request) {
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate(['name' => 'required']);
        ShiftTemplate::create([
            'name' => $request->name, 'short_code' => substr($request->name, 0, 3),
            'start_time' => $request->start_time, 'end_time' => $request->end_time,
            'grace_minutes' => (int)$request->input('grace_minutes', 5),
            'is_off_day' => $request->has('is_off_day'), 'color_theme' => 'emerald',
        ]);
        return back()->with('success', 'Custom Shift Template added!');
    }

    public function deleteShift($id) {
        if (Auth::user()->role !== 'admin') abort(403);
        ShiftTemplate::findOrFail($id)->delete();
        return back()->with('success', 'Shift template deleted.');
    }

    public function unlockOrDeleteDraft(Request $request, $userId) {
        if (Auth::user()->role !== 'admin') abort(403);
        $month = (int)$request->input('month');
        $year  = (int)$request->input('year');
        if ($request->input('mode') === 'delete') {
            WeeklyRoster::where('user_id', $userId)->where('month', $month)->where('year', $year)->delete();
            DailyRoster::where('user_id', $userId)->whereYear('duty_date', $year)->whereMonth('duty_date', $month)->delete();
            return back()->with('success', 'Roster deleted.');
        } else {
            WeeklyRoster::where('user_id', $userId)->where('month', $month)->where('year', $year)->update(['is_submitted' => false]);
            return back()->with('success', 'Draft unlocked.');
        }
    }
}
