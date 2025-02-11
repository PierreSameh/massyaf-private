<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = auth()->id(); // Get the logged-in user's ID
        $wishlists = Wishlist::with('unit.images')->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            "data" => $wishlists
        ], 200);
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
        ]);
        $unit = Wishlist::where('user_id', auth()->id())
            ->where('unit_id', $validated['unit_id'])->first();
        if ($unit) {
            $unit->delete();
            return response()->json([
                "success" => false,
                "message" => "تم حذف الوحدة من المفضلة"
            ], 201);
        }
        $wishlist = Wishlist::create([
            'user_id' => auth()->id(),
            'unit_id' => $validated['unit_id'],
        ]);

        return response()->json([
            "success" => true,
            'message' => 'تم اضافة الوحدة لقائمة المفضلة',
            'wishlist' => $wishlist
        ], 201);
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$wishlist) {
            return response()->json(['message' => 'العنصر غير موجود في قائمة المفضلة'], 404);
        }

        $wishlist->delete();

        return response()->json([
            "success" => true,
            'message' => 'تم ازالة العنصر من قائمة المفضلة'
        ], 200);
    }
}
