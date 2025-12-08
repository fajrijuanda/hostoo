<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDatabase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'db_username', 'db_password', 'description'];

    protected $casts = [
        'db_password' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
