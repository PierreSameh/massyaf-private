<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'read',
        'type',
        'ref_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
