<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function createDoctor(Request $request)
    {

        $doctor = new DoctorController();
        return $doctor->store($request);

        // return response()->json(['data' => $doctor], 201);
    }

    public function createClinic(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        $clinic = Clinic::create([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'admin_id' => Auth()->id()
        ]);

        return response()->json(['data' => $clinic], 201);
    }

    public function getDoctors()
    {
        $doctors = Doctor::with(['user','clinic'])->get();
        return response()->json(['data' => $doctors], 200);
    }

    public function getClinics()
    {
        $clinics = Clinic::all();
        return response()->json(['data' => $clinics], 200);
    }
}
