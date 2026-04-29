<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['user_id', 'file_path', 'file_name', 'file_size', 'status', 'feedback', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
        
    }
    protected $attributes = [
    'status' => 'pending'
];
 protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}