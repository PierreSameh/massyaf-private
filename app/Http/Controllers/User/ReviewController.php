<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Unit;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request){
        try{
            $request->validate([
                "rate"=> "required|numeric|min:1|max:5",
                "unit_id"=> "required|exists:units,id",
                "comment" => "nullable|string|max:1000"
            ]);

            $userId = auth()->user()->id;

            $review = Review::create([
                "user_id" => $userId,
                "unit_id" => $request->unit_id,
                "rate" => $request->rate,
                "comment"=> $request->comment ?? null
            ]);

            $unit = Unit::find($request->unit_id);
            $unitReviews = $unit->reviews()->get();
            $unit->rate = $unitReviews->avg('rate');
            $unit->save();

            return response()->json([
                "success" => true,
                "message" => "تم تقييم الوحدة بنجاح"
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => "حدث خطاء في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
