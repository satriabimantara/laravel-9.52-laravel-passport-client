<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;
    protected $fillable = [
        'access_token',
        'expires_in',
        'refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function hasExpired()
    {
        // check apakah waktu sekarang lebih besar dari masa expires_in dari access_token?
        return now()->gte($this->updated_at->addSeconds($this->expires_in));
    }
}
