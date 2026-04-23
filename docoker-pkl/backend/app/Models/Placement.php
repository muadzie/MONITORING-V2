<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    protected $table = 'placements';
    
    protected $fillable = [
        'student_id',
        'company_id',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}