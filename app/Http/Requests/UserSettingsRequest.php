<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' =>'required|string|min:2|max:50',
            'email' =>'required|email|unique:users,email,' .auth('sanctum')->user()->id,
            'phone_number' => 'required|string|max:16|unique:users,phone_number,'.auth('sanctum')->user()->id,
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'id_image' => 'nullable|image|mimes:jpg'
        ];
    }
}
