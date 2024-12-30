<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'owner_id',
        'type',
        'name',
        'rate',
        'unit_type_id',
        'city_id',
        'compound_id',
        'hotel_id',
        'status',
        'address',
        'lat',
        'lng',
        'unit_number',
        'floors_count',
        'elevator',
        'area',
        'distance_unit_beach',
        'beach_unit_transportation',
        'distance_unit_pool',
        'pool_unit_transportation',
        'room_count',
        'toilet_count',
        'images',
        'videos',
        'description',
        'reservation_roles',
        'reservation_type',
        'price',
        'insurance_amount',
        'max_individuals',
        'youth_only',
        'min_reservation_days',
        'deposit',
        'upon_arival_price',
        'weekend_prices',
        'min_weekend_period',
        'weekend_price',
    ];

    protected $appends = ['min_price', 'max_price'];

    public function getMaxPriceAttribute()
{
    // Get the unit's base price
    $basePrice = $this->price;
    
    // Get the highest special reservation time price
    $maxSpecialPrice = $this->specialReservationTimes()
        ->max('price');
    
    // Return the higher of the two prices
    return (float) max($basePrice, $maxSpecialPrice ?? 0);
}

public function getMinPriceAttribute()
{
    // Get the unit's base price
    $basePrice = $this->price;
    
    // Get all active sales that could affect the current price
    $currentSales = $this->sales()
        // ->where('from', '<=', now())
        // ->where('to', '>=', now())
        ->get();
    
    // If there are no active sales, return the base price
    if ($currentSales->isEmpty()) {
        return $basePrice;
    }
    
    // Calculate prices after applying each sale percentage
    $pricesAfterSales = $currentSales->map(function ($sale) use ($basePrice) {
        $discountAmount = ($basePrice * $sale->sale_percentage) / 100;
        return $basePrice - $discountAmount;
    });
    
    // Return the lowest price after applying sales
    return (float) $pricesAfterSales->min();
}

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function compound(){
        return $this->belongsTo(Compound::class);
    }

    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }

    public function unitType(){
        return $this->belongsTo(Type::class);
    }
    
    public function additionalFees(){
        return $this->hasMany(AdditionalFee::class);
    }

    public function availableDates(){
        return $this->hasMany(AvailableDate::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

    public function cancelPolicies(){
        return $this->hasMany(CancelPoliciy::class);
    }

    public function longTermReservations(){
        return $this->hasMany(LongTermReservations::class);
    }

    public function specialReservationTimes(){
        return $this->hasMany(SpecialReservationTimes::class);
    }

    public function images(){
        return $this->hasMany(UnitImage::class);
    }

    public function videos(){
        return $this->hasMany(UnitVideo::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenitie::class, 'unit_amenities');
    }

    public function rooms(){
        return $this->hasMany(Room::class);
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
}
