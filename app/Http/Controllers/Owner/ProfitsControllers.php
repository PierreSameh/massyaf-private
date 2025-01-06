<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitsControllers extends Controller
{
    public function calculateProfits()
    {
        $user = auth()->user();
    
        // Calculate total profits
        $totalProfits = Reservation::whereRelation('unit', 'owner_id', $user->id)
            ->where('paid', 1)
            ->where('status', 'approved')
            ->sum('owner_profit');
    
        // Calculate monthly units profits
        $monthlyUnitsProfits = Reservation::whereRelation('unit', 'owner_id', $user->id)
            ->whereRelation('unit', 'type', 'unit')
            ->where('paid', 1)
            ->where('status', 'approved')
            ->select(DB::raw('YEAR(date_from) as year, MONTH(date_from) as month, SUM(owner_profit) as profit'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => $row->year,
                    'month' => Carbon::createFromDate($row->year, $row->month, 1)->format('F'),
                    'profit' => $row->profit,
                ];
            });

        // Calculate monthly hotels profits
        $monthlyHotelsProfits = Reservation::whereRelation('unit', 'owner_id', $user->id)
            ->whereRelation('unit', 'type', 'hotel')
            ->where('paid', 1)
            ->where('status', 'approved')
            ->select(DB::raw('YEAR(date_from) as year, MONTH(date_from) as month, SUM(owner_profit) as profit'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => $row->year,
                    'month' => Carbon::createFromDate($row->year, $row->month, 1)->format('F'),
                    'profit' => $row->profit,
                ];
            });
    
        return response()->json([
            "success" => true,
            "totalProfits" => $totalProfits,
            "monthlyUnitsProfits" => $monthlyUnitsProfits,
            "monthlyHotelsProfits" => $monthlyHotelsProfits
        ], 200);
    }
    

    public function unitsProfits()
    {
        $user = auth()->user();
    
        // Get all hotel units owned by the authenticated user with approved and paid reservations
        $units = Unit::where('owner_id', $user->id)
            ->where('type', 'unit')
            ->whereHas('reservations', function ($query) {
                $query->where('paid', 1)->where('status', 'approved');
            })
            ->with(['reservations' => function ($query) {
                $query->where('paid', 1)->where('status', 'approved');
            }])
            ->get();
    
        $totalProfits = 0;
        $unitsProfits = [];
    
        foreach ($units as $unit) {
            // Calculate profits for this unit
            $unitProfit = $unit->reservations->sum('owner_profit');
    
            // Prepare reservations data
            $reservationsData = $unit->reservations->map(function ($reservation) {
                return [
                    'reservation_id' => $reservation->id,
                    'date_from' => $reservation->date_from,
                    'days_count' => $reservation->days_count,
                    'owner_profit' => $reservation->owner_profit,
                ];
            });
    
            // Add unit's profit to total profits
            $totalProfits += $unitProfit;
    
            // Add unit's profit and reservation details to the units profits array
            $unitsProfits[] = [
                'unit_id' => $unit->id,
                'unit_name' => $unit->name, // Assuming the unit has a 'name' attribute
                'profit' => $unitProfit,
                'reservations' => $reservationsData,
            ];
        }
    
        return response()->json([
            "success" => true,
            "totalProfits" => $totalProfits,
            "units" => $unitsProfits
        ], 200);
    }

    public function hotelsProfits()
    {
        $user = auth()->user();
    
        // Get all hotel units owned by the authenticated user with approved and paid reservations
        $units = Unit::where('owner_id', $user->id)
            ->where('type', 'hotel')
            ->whereHas('reservations', function ($query) {
                $query->where('paid', 1)->where('status', 'approved');
            })
            ->with(['reservations' => function ($query) {
                $query->where('paid', 1)->where('status', 'approved');
            }])
            ->get();
    
        $totalProfits = 0;
        $unitsProfits = [];
    
        foreach ($units as $unit) {
            // Calculate profits for this unit
            $unitProfit = $unit->reservations->sum('owner_profit');
    
            // Prepare reservations data
            $reservationsData = $unit->reservations->map(function ($reservation) {
                return [
                    'reservation_id' => $reservation->id,
                    'date_from' => $reservation->date_from,
                    'days_count' => $reservation->days_count,
                    'owner_profit' => $reservation->owner_profit,
                ];
            });
    
            // Add unit's profit to total profits
            $totalProfits += $unitProfit;
    
            // Add unit's profit and reservation details to the units profits array
            $unitsProfits[] = [
                'unit_id' => $unit->id,
                'unit_name' => $unit->name, // Assuming the unit has a 'name' attribute
                'profit' => $unitProfit,
                'reservations' => $reservationsData,
            ];
        }
    
        return response()->json([
            "success" => true,
            "totalProfits" => $totalProfits,
            "units" => $unitsProfits
        ], 200);
    }
    
    
    

}
