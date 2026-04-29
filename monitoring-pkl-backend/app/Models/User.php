<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

   protected $fillable = [
    'name', 
    'email', 
    'password', 
    'nisn', 
    'nip',
    'kelas',
    'jurusan',
    'mata_pelajaran',
    'bidang_usaha',
    'kontak_person',
    'phone', 
    'role_id', 
    'company_id', 
    'teacher_id'
];

    protected $hidden = ['password', 'remember_token'];

    // Relasi
    public function role()
    {
         return $this->belongsTo(Role::class)->select(['id', 'name']);
    }

    public function company()
    {
         return $this->belongsTo(Company::class)->select(['id', 'name', 'address', 'latitude', 'longitude', 'radius']);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function supervisedStudents()
{
    return $this->hasMany(User::class, 'teacher_id');
}

    public function students()
    {
        return $this->hasMany(User::class, 'teacher_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role_id', 2);
    }

    public function scopeTeachers($query)
    {
        return $query->where('role_id', 3);
    }

    public function scopeCompanies($query)
    {
        return $query->where('role_id', 4);
    }
    
public function assessmentsAsAssessor()
{
    return $this->hasMany(Assessment::class, 'assessor_id');
}

public function assessmentsAsStudent()
{
    return $this->hasMany(Assessment::class, 'student_id');
}

public function finalReports()
{
    return $this->hasMany(FinalReport::class, 'student_id');
}

public function notifications()
{
    return $this->hasMany(Notification::class);
}

public function unreadNotifications()
{
    return $this->notifications()->where('is_read', false);
}

// Relasi ke kelas
public function class()
{
    return $this->belongsTo(Classes::class, 'class_id');
}

// Relasi ke siswa dalam kelas (untuk teacher)
public function classStudents()
{
    return $this->hasMany(User::class, 'class_id');
}

// Relasi ke penempatan
public function placements()
{
    return $this->hasMany(Placement::class, 'student_id');
}

public function activePlacement()
{
    return $this->hasOne(Placement::class, 'student_id')->where('status', 'active');
}

}