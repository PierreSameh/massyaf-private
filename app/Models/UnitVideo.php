<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UnitVideo extends Model
{
    protected $fillable = ["unit_id", "video"];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }


    public function getVideoAttribute($value)
    {
        // Assuming the 'picture' column stores the image path
        return $value ? url(Storage::url("app/public/" . $value)) : null;
    }
}
