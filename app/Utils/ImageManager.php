<?php
namespace App\Utils;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;

class ImageManager{
    public static function UploadImages($request, $user=null, $unit=null)
    {
    // Implement images upload logic here
        if($request->hasFile('images'))
            {//multi images
                foreach($request->images as $image)
                {
                    $imageName = self::generateImageName($image);
                    $path = self::storeImageInLocal($image, $imageName, 'units');
                    $unit->images()->create([
                        'path'=>$path
                    ]);
                }
            }
        // Upload single image for User image
        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            // delete the image from the local
            self::deleteImageFromLocal($user->image);
            $imageName = self::generateImageName($image);
            $path = self::storeImageInLocal($image, $imageName, 'users');
            //update image in database
            $user->update([
                'image'=>$path
            ]);
        }
    }
    public static function uploadIdImage($request, $user)
    {
        //upload id_image
        if($request->hasFile('id_image'))
        {
            $image = $request->file('id_image');
            // delete the image from the local
            self::deleteImageFromLocal($user->id_image);
            $imageName = self::generateImageName($image);
            $path = self::storeImageInLocal($image, $imageName, 'ID_IMAGE');
            //update image in database
            $user->update([
                'id_image'=>$path
        ]);
        }
    }
    public static function deleteImages($unit)
    {
        if ($unit->images->count() > 0)
        {
            foreach ($unit->images as $image)
            {
                self::deleteImageFromLocal($image->path);
                $image->delete();
            }
        }
    }
    public static function generateImageName($image)
    {
        // Implement logic to generate unique image name here
        $imageName = Str::uuid() . time() . $image->getClientOriginalExtension();
        return $imageName;
    }
    public static function storeImageInLocal($image, $imageName, $path)
    {
        // upload the new image in local storage
        $path = $image->storeAs('uploads/'.$path, $imageName, ['disk'=> 'uploads']);
        return $path;
    }
    public static function deleteImageFromLocal($image_path)
    {
        if (File::exists(public_path( $image_path )))
        {
            File::delete(public_path( $image_path ));
        }
    }
}
