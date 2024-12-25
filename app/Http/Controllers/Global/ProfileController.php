<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            "old_password" => 'required',
            'password' => 'required|string|min:8|
            regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/u
            |confirmed',
            ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message"=> $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        $old_password = $request->old_password;

        if ($user) {
            if (!Hash::check($old_password, $user->password)) {
                return response()->json([
                    "success"=> false,
                    "message"=> "كلمة المرور الحالية غير صحيحة"
                ], 400);
            }
            if($old_password == $request->password){
                return response()->json([
                    "success"=> false,
                    "message"=> "كلمة المرور الجديدة يجب ان تختلف عن كلمة المرور الحالية"
                ], 400);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                "success"=> true,
                "message"=> "تم تغيير كلمة المرور بنجاح",
            ], 200);
        }
    }

    public function get(Request $request) {
        $user = $request->user();
        return response()->json([
            "success"=> true,
            "message"=> "تم جلب بيانات المستخدم بنجاح",
            "data" => $user
            ],200);
    }

    public function update(Request $request) {
        try{
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'image' => "nullable|image|mimes:jpeg,jpg,png|max:2048",
            'id_image' => "nullable|image|mimes:jpg",
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message"=> $validator->errors()->first(),
            ], 422);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->hasFile('image')) {
            // Delete old picture if exists
            if ($user->image) {
                $oldPicturePath = public_path('storage/' . $user->image);
                if (file_exists($oldPicturePath)) {
                    unlink($oldPicturePath);
                }
            }
    
            // Store new picture
            $newPicturePath = $request->file('image')->store('user_pictures', 'public');
            $user->image = $newPicturePath;
        }
        if ($request->hasFile('id_image')) {
            // Delete old picture if exists
            if ($user->id_image) {
                $oldPicturePath = public_path('storage/' . $user->id_image);
                if (file_exists($oldPicturePath)) {
                    unlink($oldPicturePath);
                }
            }
    
            // Store new picture
            $newPicturePath = $request->file('id_image')->store('user_pictures', 'public');
            $user->id_image = $newPicturePath;
        }
        $user->save();
        return response()->json([
                "success"=> true,
                "message"=> "تم تحديث الملف الشخصي بنجاح",
                "data" => $user
        ], 200);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => "حدث خطاء في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
