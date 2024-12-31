<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReservationId extends Model
{
    protected $fillable = ['reservation_id', 'path'];

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function getPathAttribute($value)
    {
        // Assuming the 'picture' column stores the image path
        return $value ? url(Storage::url("app/public/" . $value)) : null;
    }
}
