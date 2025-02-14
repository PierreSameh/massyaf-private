<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\City;
use App\Models\Compound;
use App\Models\Hotel;
use App\Services\HomeService;
use Illuminate\Http\Request;

class CompinedHomeController extends Controller
{
    protected $service;

    public function __construct(HomeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {

        $sales = $this->service->sales();
        $ads = Ad::latest()->get();
        $compounds = Compound::inRandomOrder()->get();
        $cities = City::inRandomOrder()->get();
        $hotels = Hotel::inRandomOrder()->get();
        $topRated = $this->service->topRated();
        $bestSeller = $this->service->bestSeller();


        return response()->json([
            "success" => true,
            "data" => [
                "sales" => $sales,
                "best_seller" => $bestSeller,
                "ads" => $ads,
                "cities" => $cities,
                "compounds" => $compounds,
                "hotels" => $hotels,
                "topRated" => $topRated
            ]
        ]);
    }
}
