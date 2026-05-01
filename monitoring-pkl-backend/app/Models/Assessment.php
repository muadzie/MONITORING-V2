<?php
// app/Models/Assessment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';
    
    protected $fillable = [
        'student_id',
        'assessor_id',
        'assessor_type',
        'attendance_score',
        'logbook_score',
        'report_score',
        'attitude_score',
        'performance_score',
        'discipline_score',
        'final_score',
        'notes'
    ];
    
    protected $casts = [
        'attendance_score' => 'integer',
        'logbook_score' => 'integer',
        'report_score' => 'integer',
        'attitude_score' => 'integer',
        'performance_score' => 'integer',
        'discipline_score' => 'integer',
        'final_score' => 'decimal:2'
    ];
    
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }
}