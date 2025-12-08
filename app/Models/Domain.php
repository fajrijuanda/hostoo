<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'user_id', 
        'domain_name', 
        'status', 
        'verified',
        'ftp_host',
        'ftp_username',
        'ftp_password',
        'cyberpanel_website_id'
    ];

    protected $casts = [
        'ftp_password' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
