<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';
    
    protected $fillable = [
        'name',
        'teacher_id',
        'academic_year',
    ];
    
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    
    public function students()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}