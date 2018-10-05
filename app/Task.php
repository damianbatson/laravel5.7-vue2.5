<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['user_id', 'task', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
