<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'doctor_id',
        'patient_id',
        'appointment_time',
        'symptoms',
        'diagnosis',
        'prescription',
        'next_appointment',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'next_appointment' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id','user_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class,'patient_id','user_id');
    }
}
