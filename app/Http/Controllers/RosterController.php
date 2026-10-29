<?php
namespace App\Http\Controllers;
use App\Models\Roster;
use App\Models\Doctor;
use Illuminate\Http\Request;

class RosterController extends Controller {
    public function index() {
        $rosters = Roster::with('doctor')->orderBy('duty_date', 'desc')->get();
        return view('rosters.index', compact('rosters'));
    }

    public function create() {
        $doctors = Doctor::all();
        return view('rosters.create', compact('doctors'));
    }

    public function store(Request $request) {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'duty_date' => 'required|date',
            'shift' => 'required|string',
        ]);
        
        Roster::create($request->all());
        return redirect()->route('rosters.index')->with('success', 'Roster created successfully.');
    }
}
