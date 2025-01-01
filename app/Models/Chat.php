<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'owner_id',
        'created_at'
    ];

    public function messages(){
        return $this->hasMany(Message::class, 'chat_id');
    }

    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function owner(){
        return $this->belongsTo(User::class,'owner_id');
    }

    protected $hidden = [ 'updated_at'];

}
