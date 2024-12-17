<?php

namespace App\Http\Controllers\api\Auth;

use App\Models\User;
use App\Utils\ImageManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserSettingsRequest;

class ProfileController extends Controller
{
    public function updateProfile(UserSettingsRequest $request)
    {
        $request->validated();
        // code to update user settings goes here
        $user = User::find(auth('sanctum')->user()->id);
        $user->update($request->all());
        ImageManager::UploadImages($request, $user);
        ImageManager::uploadIdImage($request, $user);

        return responseApi(200, 'User settings updated successfully');
    }
}
