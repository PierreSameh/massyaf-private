<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'unit_id',
        'bed_count',
        'bed_sizes',
    ];

    protected $casts = [
        'bed_sizes' => 'array',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function amenities()
{
    return $this->belongsToMany(Amenitie::class, 'room_amenities');
}

}
