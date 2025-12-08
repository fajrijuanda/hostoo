<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GithubSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'repo_url', 'branch', 'public_key', 'private_key', 'webhook_secret'
    ];

    protected $casts = [
        'private_key' => 'encrypted', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
