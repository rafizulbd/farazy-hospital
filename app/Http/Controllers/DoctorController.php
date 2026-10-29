<?php
namespace App\Http\Controllers;
use App\Models\Doctor;
use Illuminate\Http\Request;
class DoctorController extends Controller {
    public function index() {
        $doctors = Doctor::all();
        return view('doctors.index', compact('doctors'));
    }
    public function create() {
        return view('doctors.create');
    }
    public function store(Request $request) {
        $request->validate(['name' => 'required']);
        Doctor::create($request->all());
        return redirect()->route('doctors.index');
    }
}
