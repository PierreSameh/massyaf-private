<?php

namespace App\Http\Controllers\api\Auth;

use App\Models\User;
use App\Models\Admin;
use App\Utils\ImageManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            'password' => Hash::make($request->password),
        ]);
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
            $user->save();
        }
        if (!$user) {
            return responseApi(404, 'User not found');
        }

        $token = $user->createToken('user_token')->plainTextToken;

        // $user->notify(new SendOtpNotify());
        // DB::commit();
        return responseApi(201, $request->type . ' registered successfully', ['token' => $token, "user" => $user]);
        // } catch (\Exception ) {
        //     DB::rollBack();
        //     return responseApi(500, 'Internal Server Error');
        // }
    }
    public function askCode(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                $code = rand(1000, 9999);

                $user->last_otp = Hash::make($code);
                $user->last_otp_expire = Carbon::now()->addMinutes(10)->timezone('Africa/Cairo');
                $user->save();

                $message = "الرمز التعريفي الخاص بك هو " . $code;

                Log::info($message);

                return response()->json([
                    "success" => true,
                    "message" => "تم ارسال الرمز بنجاح",
                    "notes" => "تنتهي صلاحية الرمز خلال 10 دقائق"
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "حدث خطأ في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "code" => ["required", "numeric", "digits:4"],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        $code = $request->code;

        if ($user) {
            if (!Hash::check($code, $user->last_otp ? $user->last_otp : Hash::make(00000))) {
                return response()->json([
                    "success" => false,
                    "message" => "الرمز غير صحيح"
                ], 400);
            } else {
                $timezone = 'Africa/Cairo'; // Replace with your specific timezone if different
                $verificationTime = new Carbon($user->last_otp_expire, $timezone);
                if ($verificationTime->isPast()) {
                    return response()->json([
                        "success" => false,
                        "message" => "تم انتهاء صلاحية الرمز"
                    ], 400);
                } else {
                    $user->phone_verified_at = 1;
                    $user->save();

                    return response()->json([
                        "success" => true,
                        "message" => "تم التحقق بنجاح"
                    ], 200);
                }
            }
        }
    }

    public function sendForgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "phone" => 'required|exists:users,phone_number', // Can be email or phone
        ], [
            "required" => "المعرف مطلوب.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first(),
            ]);
        }

        // Determine whether the identifier is an email or a phone number
        $user = User::where('phone_number', $request->phone)->first();

        if ($user) {
            $code = rand(10000, 99999);
            $user->last_otp = Hash::make($code);
            $user->last_otp_expire = Carbon::now()->addMinutes(10)->timezone('Africa/Cairo');
            $user->save();

            $message = "رمز التحقق الخاص بك هو " . $code;

            return response()->json([
                "success" => true,
                "message" => "تم إرسال رمز التحقق.",
                "notes" => [
                    "الرمز ينتهي صلاحيته بعد 10 دقائق.",
                    "يمكن استخدام نفس الرابط لإعادة إرسال الرمز."
                ]
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "لم يتم تسجيلك.",
            ]);
        }
    }

    public function forgetPasswordCheckCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "phone" => ["required", "exists:users,phone_number"], // Can be email or phone
            "code" => ["required", "numeric", "digits:5"],
        ], [
            "required" => "هذا الحقل مطلوب.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $user = User::where('phone_number', $request->phone)->first();

        if ($user && Hash::check($request->code, $user->last_otp)) {
            $verificationTime = new Carbon($user->last_otp_expire, 'Africa/Cairo');
            if ($verificationTime->isPast()) {
                return response()->json([
                    "success" => false,
                    "message" => "الرمز منتهي الصلاحية.",
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "تم التحقق من الرمز.",
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "الرمز غير صحيح.",
        ]);
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "phone" => ["required", "exists:users,phone_number"], // Email or phone
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/u',
                'confirmed'
            ],
        ], [
            "password.regex" => "كلمة المرور يجب أن تحتوي على حرف واحد على الأقل ورقم ورمز خاص."
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first(),
            ]);
        }

        $user = User::where('phone_number', $request->phone)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                "success" => true,
                "message" => "تم تغيير كلمة المرور بنجاح.",
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "لم يتم تسجيلك.",
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('password');
        $identifier = $request->identifier; // Can be email or phone

        $user = User::where('phone_number', $identifier)
            ->orWhere('email', $request->identifier) // Add prefix
            ->first();

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])) {
            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                "success" => true,
                "message" => "تم تسجيل الدخول بنجاح.",
                "data" => ["token" => $token, "user" => $user],
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "بيانات الاعتماد غير صحيحة.",
        ], 400);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return responseApi(200, 'تم تسجيل الخروج بنجاح');
    }
    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $user->delete();
        return responseApi(200, 'تم حذف الحساب بنجاح');
    }
}
