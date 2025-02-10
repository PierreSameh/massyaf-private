<?php

namespace App\Services;

use App\Models\City;
use App\Models\Compound;
use App\Models\Hotel;
use App\Models\Unit;

class HomeService
{
    public function index(){
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])
        ->where('status', 'active')
        ->latest()->get();

        return $units;
    }

    public function sales(){
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])
        ->where('status', 'active')
        ->has('sales') // Filter units that have sales
        ->inRandomOrder() // Get the units in random order
        ->get();

        return $units;
    }

    public function topRated(){
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])
        ->where('status', 'active')
        ->orderBy('rate', 'desc')->get();

        return $units;
    }

    public function bestSeller(){
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])
        ->where('status', 'active')
        ->withCount(['reservations' => function($query) {
            $query->where('status', 'approved')
                ->where('created_at', '>=', now()->subMonths(3)); // Last 3 months
        }])
        ->orderByDesc('reservations_count')
        ->take(10)  // Limit results
        ->get();

        return $units;
    }

    public function typeSales($data){

        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])
        ->where('status', 'active')
        ->where('type', $data['type'])
        ->has('sales') // Filter units that have sales
        ->inRandomOrder() // Get the units in random order
        ->get();

        return $units;
    }
}