<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_type',
        'chat_id',
        'message',
        'seen',
        'created_at',
    ];

    public $timestamps = false;

    protected $hidden = [ 'updated_at'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s', // Keep the format without timezone conversion
    ];
    public function chat(){
        return $this->belongsTo(Chat::class);
    }
}
