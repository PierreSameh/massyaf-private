<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_id',
        'owner_id',
    ];

    public function messages(){
        return $this->hasMany(Message::class, 'sender_id');
    }

}
