<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Attempt to log the user in
        if (Auth::guard('admin')->attempt(['name' => $request->username, 'password' => $request->password])) {
            $user = Admin::where("name", $request->username)->first();
    
            $token = $user->createToken('AdminAccessToken')->plainTextToken;
    
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token, 
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid username or password',
            ], 401);
        }
    }

    public function logout(Request $request)
{   
    try{
        // Revoke all tokens for the admin
        $admin = $request->user();
        $admin->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطاء في الخادم',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
