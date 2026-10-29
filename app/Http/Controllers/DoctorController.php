<?php
namespace App\Http\Controllers;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller {
    public function index() {
        $doctors = Doctor::with('user')->get();
        return view('doctors.index', compact('doctors'));
    }
    public function create() {
        return view('doctors.create');
    }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email'
        ]);
        $doctor = Doctor::create($request->only(['name', 'email', 'specialization', 'phone', 'room_no']));

        if ($request->filled('email')) {
            User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $doctor->name,
                    'password' => Hash::make($request->login_password ?: 'password123'),
                    'whatsapp_number' => $doctor->phone,
                    'role' => 'doctor',
                    'doctor_id' => $doctor->id,
                    'is_password_changed' => false,
                ]
            );
        }
        return redirect()->route('doctors.index')->with('success', 'Doctor & Login ID created successfully!');
    }
}
