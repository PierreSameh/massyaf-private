<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenitie extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_global'
    ];
    protected $casts = [
        'is_global' => 'boolean',
    ];
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_amenities');
    }
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_amenities');
    }
}
