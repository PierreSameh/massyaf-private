<?php

namespace App\Http\Controllers\api\Auth;

use App\Models\User;
use App\Utils\ImageManager;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        $request->validated();
        // try{
        //     DB::beginTransaction();
            $user = User::create([
                'type' => $request->post('type'),
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'phone_number' => $request->post('phone_number'),
                'image'=> $request->post('image') ?? asset('User-Profile-PNG-Image.png'),
            ]);

            if (!$user) {
                return responseApi(404, 'User not found');
            }

            if($request->hasFile('image')){
                ImageManager::UploadImages($request, $user, null);
            }
            $token = $user->createToken('user_token')->plainTextToken;

            // $user->notify(new SendOtpNotify());
            // DB::commit();
            return responseApi(201, 'User registered successfully', ['token'=>$token]);
        // } catch (\Exception ) {
        //     DB::rollBack();
        //     return responseApi(500, 'Internal Server Error');
        // }
    }
    public function logout()
    {
        $user = auth('sanctum')->user();
        $user->currentAccessToken()->delete();
        return responseApi(200, 'User logged out successfully');
    }
    public function deleteAccount()
    {
        $user = auth('sanctum')->user();
        $user->delete();
        return responseApi(200, 'Delete account successfully');
    }
}
