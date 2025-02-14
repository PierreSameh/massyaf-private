<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'muted_for_user1',
        'muted_for_user2',
        'created_at',
        'admin_notified'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public $timestamps = false;

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    protected $hidden = ['updated_at'];

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }
}
