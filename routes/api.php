<?php

use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\AppointmentController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ClinicController;
use App\Http\Controllers\Api\v1\DoctorController;
use App\Http\Controllers\Api\v1\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::get('/doctors', [AdminController::class, 'getDoctors']);

    // Admin routes
    Route::group(['middleware' => ['admin']], function () {
        Route::post('/doctors', [AdminController::class, 'createDoctor']);
        Route::post('/clinics', [AdminController::class, 'createClinic']);
        Route::get('/clinics', [AdminController::class, 'getClinics']);
        Route::get('/all_patients', [PatientController::class, 'index']);
        Route::get('/new_patients_per_day', [PatientController::class, 'getNewPatientsPerDay']);
    });

    // Clinic routes
    Route::get('/appointments', [ClinicController::class, 'getAppointments']);

    // Doctor routes
    Route::group(['middleware' => ['doctor']], function () {
        Route::get('/doctor_appointments', [AppointmentController::class, 'getDoctorAppointments']);
        Route::post('/next_appointment', [AppointmentController::class, 'nextAppointment']);
    });

    // Patient routes
    Route::group(['middleware' => ['patient']], function () {
        Route::get('/appointments', [AppointmentController::class, 'getAppointments']);
        Route::post('/appointments', [AppointmentController::class, 'makeAppointment']);
    });
});
