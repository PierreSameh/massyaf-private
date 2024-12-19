<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitVideo extends Model
{
    protected $fillable = ["unit_id", "video"];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
