<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('user')->get();
        return response()->json(['patients' => $patients]);
    }

    public function show($id)
    {
        $patient = User::with('appointments')->find($id);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        return response()->json(['patient' => $patient]);
    }

    public function update(Request $request, $id)
    {
        $patient = User::find($id);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        $patient->name = $request->input('name');
        $patient->email = $request->input('email');
        $patient->password = Hash::make($request->input('password'));
        $patient->save();

        return response()->json(['patient' => $patient]);
    }

    public function destroy($id)
    {
        $patient = User::find($id);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        $patient->delete();

        return response()->json(['message' => 'Patient deleted']);
    }

    public function getNewPatientsPerDay()
    {
        // Query the database for new patients per day
        $newPatients = Patient::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->get();

        // Format the data for Chart.js
        $labels = $newPatients->pluck('date');
        $data = $newPatients->pluck('count');

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'New Patients Per Day',
                    'backgroundColor' => '#f87979',
                    'data' => $data,
                ]
            ]
        ];

        return response()->json($chartData);
    }
}
