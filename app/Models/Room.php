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

    public function getBedSizesAttribute($value){
        // Decode the value twice if it was double-encoded
        $decodedValue = json_decode($value, true);
        
        // Check if it's still a string and needs another decoding
        if (is_string($decodedValue)) {
            $decodedValue = json_decode($decodedValue, true);
        }
    
        return $decodedValue;
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function amenities()
{
    return $this->belongsToMany(Amenitie::class, 'room_amenities');
}

}
