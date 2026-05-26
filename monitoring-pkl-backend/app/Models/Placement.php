<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    protected $table = 'placements';
    
    protected $fillable = [
        'student_id',
        'company_id',
        'teacher_id',
        'start_date',
        'end_date',
        'status',
        'notes',
        'shift_start',
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeStudentIdsByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId)->where('status', 'active')->pluck('student_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}