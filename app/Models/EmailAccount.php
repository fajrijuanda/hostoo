<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailAccount extends Model
{
    protected $fillable = ['user_id', 'email', 'password'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
