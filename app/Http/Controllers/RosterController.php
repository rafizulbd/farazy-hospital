<?php
namespace App\Http\Controllers;
use App\Models\Doctor;
use App\Models\RosterSetting;
use App\Models\WeeklyRoster;
use App\Models\ShiftSwap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RosterController extends Controller {
    public function index(Request $request) {
        $setting = RosterSetting::first();
        $doctors = Doctor::with('weeklyRoster')->get();
        $user = Auth::user();

        // Admin chaile jekono doctor-er profile select kore test/manage korte parbe
        $activeDoctorId = $user->doctor_id;
        if ($user->role === 'admin') {
            $activeDoctorId = $request->query('doctor_id', $doctors->first()->id ?? null);
        }
        $myDoctor = $activeDoctorId ? Doctor::with('weeklyRoster')->find($activeDoctorId) : null;
        $myRoster = $myDoctor ? $myDoctor->weeklyRoster : null;

        $swaps = ShiftSwap::with(['requester', 'target'])->latest()->get();

        return view('rosters.index', compact('setting', 'doctors', 'myDoctor', 'myRoster', 'swaps'));
    }

    public function updateSettings(Request $request) {
        if (Auth::user()->role !== 'admin') abort(403);
        $setting = RosterSetting::first();
        $setting->update($request->only([
            'title', 'validity_label', 'notice_text', 'current_phase',
            'phase1_date', 'phase2_date', 'phase3_date', 'approved_by',
            'active_month', 'active_year'
        ]));
        return back()->with('success', 'Roster Phase, Notice & Timeline updated successfully!');
    }

    public function saveDraft(Request $request) {
        $setting = RosterSetting::first();
        $user = Auth::user();

        if ($setting->current_phase !== 'draft_open' && $user->role !== 'admin') {
            return back()->withErrors(['msg' => 'Draft Roster submission time is currently closed!']);
        }

        $doctorId = ($user->role === 'admin' && $request->filled('doctor_id')) ? $request->doctor_id : $user->doctor_id;
        if (!$doctorId) {
            return back()->withErrors(['msg' => 'No Doctor profile linked to this account.']);
        }

        $existing = WeeklyRoster::where('doctor_id', $doctorId)->first();
        if ($existing && $existing->is_submitted && $user->role !== 'admin') {
            return back()->withErrors(['msg' => 'Your draft roster is already submitted and locked!']);
        }

        WeeklyRoster::updateOrCreate(
            ['doctor_id' => $doctorId],
            [
                'sun_shift' => $request->sun_shift,
                'mon_shift' => $request->mon_shift,
                'tue_shift' => $request->tue_shift,
                'wed_shift' => $request->wed_shift,
                'thu_shift' => $request->thu_shift,
                'fri_shift' => $request->fri_shift,
                'sat_shift' => $request->sat_shift,
                'notes'     => $request->notes,
                'is_submitted' => true,
            ]
        );

        return back()->with('success', 'Draft Roster saved and locked! You can now view other doctors\' schedules.');
    }

    public function unlockDraft($doctorId) {
        if (Auth::user()->role !== 'admin') abort(403);
        WeeklyRoster::where('doctor_id', $doctorId)->update(['is_submitted' => false]);
        return back()->with('success', 'Doctor draft unlocked for re-editing.');
    }

    public function requestSwap(Request $request) {
        $setting = RosterSetting::first();
        if ($setting->current_phase !== 'swap_open' && Auth::user()->role !== 'admin') {
            return back()->withErrors(['msg' => 'Shift Swap / Conflict resolution window is currently closed!']);
        }

        $request->validate([
            'requester_doctor_id' => 'required|exists:doctors,id',
            'target_doctor_id' => 'required|exists:doctors,id|different:requester_doctor_id',
            'day_key' => 'required|in:sun,mon,tue,wed,thu,fri,sat',
        ]);

        $reqRoster = WeeklyRoster::where('doctor_id', $request->requester_doctor_id)->first();
        $tarRoster = WeeklyRoster::where('doctor_id', $request->target_doctor_id)->first();

        if (!$reqRoster || !$tarRoster) {
            return back()->withErrors(['msg' => 'Both doctors must have a submitted roster to swap shifts.']);
        }

        $col = $request->day_key . '_shift';

        ShiftSwap::create([
            'requester_doctor_id' => $request->requester_doctor_id,
            'target_doctor_id' => $request->target_doctor_id,
            'day_key' => $request->day_key,
            'requester_shift' => $reqRoster->$col,
            'target_shift' => $tarRoster->$col,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Shift Swap Requisition sent! Waiting for approval.');
    }

    public function respondSwap(Request $request, $id) {
        $setting = RosterSetting::first();
        if ($setting->current_phase !== 'swap_open' && Auth::user()->role !== 'admin') {
            return back()->withErrors(['msg' => 'Swap window is locked!']);
        }

        $swap = ShiftSwap::findOrFail($id);
        $action = $request->input('action'); // accept or reject

        if ($action === 'accept') {
            $reqRoster = WeeklyRoster::where('doctor_id', $swap->requester_doctor_id)->first();
            $tarRoster = WeeklyRoster::where('doctor_id', $swap->target_doctor_id)->first();
            $col = $swap->day_key . '_shift';

            if ($reqRoster && $tarRoster) {
                $temp = $reqRoster->$col;
                $reqRoster->$col = $tarRoster->$col;
                $tarRoster->$col = $temp;
                $reqRoster->save();
                $tarRoster->save();
            }
            $swap->status = 'accepted';
            $swap->save();
            return back()->with('success', 'Requisition Accepted! Shifts have been automatically swapped.');
        } else {
            $swap->status = 'rejected';
            $swap->save();
            return back()->with('success', 'Swap Requisition Rejected.');
        }
    }
}
