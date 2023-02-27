<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('clinic')->get();
        return response()->json(['doctors' => $doctors]);
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'specialization' => 'required|string',
            'clinic_id' => 'required|exists:clinics,id'

        ]);

        $doctor = new User;
        $doctor->name = $request->input('name');
        $doctor->email = $request->input('email');
        $doctor->password = Hash::make($request->input('password'));
        $doctor->role = 'doctor';
        $doctor->save();

        $doctor_profile = new Doctor;
        $doctor_profile->user_id = $doctor->id;
        $doctor_profile->clinic_id = $request->input('clinic_id');
        $doctor_profile->specialization = $request->input('specialization');
        $doctor_profile->save();

        return response()->json(['doctor' => $doctor, 'doctor_profile' => $doctor_profile]);
    }

    public function getPatients()
    {
        $doctor = Doctor::where('user_id', Auth()->id())->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        $patients = $doctor->patients;

        return response()->json(['patients' => $patients]);
    }
}
