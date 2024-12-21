<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenitie extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_amenities');
    }
}
