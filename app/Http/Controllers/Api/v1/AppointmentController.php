<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function getAppointments()
    {
        $patient = Patient::where('user_id', Auth()->id())->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }
        $appointments = Appointment::where('patient_id',$patient->user_id)->with('clinic','doctor.user')->get();


        return response()->json(['appointments' => $appointments]);
    }

    public function makeAppointment(Request $request)
    {

        $validatedData = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_time' => 'required|date'
        ]);

        $patient = User::find(Auth()->id());
        $doctor = Doctor::where('user_id',$validatedData['doctor_id'])->first();
        $appointment = new Appointment();
        $appointment->patient_id = $patient->id;
        $appointment->doctor_id = $validatedData['doctor_id'];
        $appointment->clinic_id = $doctor->clinic_id;
        $appointment->appointment_time = $validatedData['appointment_time'];
        $appointment->save();

        return response()->json(['appointment' => $appointment]);
    }

    public function getDoctorAppointments()
    {
        $doctor = Doctor::where('user_id', Auth()->id())->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        $appointments = Appointment::where('doctor_id',$doctor->user_id)->with('clinic','patient.user')->get();

        return response()->json(['appointments' => $appointments]);
    }

    public function show($id)
    {
        $appointment = Appointment::with('doctor.user', 'patient.user', 'clinic')->find($id);

        if (!$appointment) {
            return response()->json(['error' => 'appointment not found'], 404);
        }

        return response()->json(['appointment' => $appointment]);
    }

    public function nextAppointment(Request $request)
    {

        $validatedData = $request->validate([
            'symptoms' => 'required|string',
            'diagnosis' => 'required|string',
            'prescription' => 'required|string',
            'next_appointment' => 'date|nullable',
            'id' => 'required|exists:appointments,id'
        ]);

        $doctor = User::find(Auth()->id());

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        $appointment = Appointment::find($validatedData['id']);
        $appointment->symptoms = $validatedData['symptoms'];
        $appointment->diagnosis = $validatedData['diagnosis'];
        $appointment->prescription = $validatedData['prescription'];
        if (!empty($validatedData['next_appointment'])) {
            $appointment->next_appointment = $validatedData['next_appointment'];
            $new = new Appointment();
            $new->patient_id = $appointment->patient_id;
            $new->doctor_id = $doctor->id;
            $new->clinic_id = $appointment->clinic_id;
            $new->appointment_time = $validatedData['next_appointment'];
            $new->save();
        }
        $appointment->save();



        return response()->json(['status' => 'success','appointment' => $appointment]);
    }
}
