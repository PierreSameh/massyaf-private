<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ["user_id", "unit_id", "rate", "comment"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
