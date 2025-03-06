<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UnitImage extends Model
{
    protected $fillable = ["unit_id", "image"];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function getImageAttribute($value)
    {
        // Assuming the 'picture' column stores the image path
        return $value ? url(Storage::url( $value)) : null;
    }
}
