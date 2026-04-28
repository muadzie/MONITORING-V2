<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['user_id', 'date', 'type', 'reason', 'attachment', 'status', 'rejection_reason'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}