<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'type' => 'sometimes',
            'name' =>'required|string|min:2|max:50',
            'email' =>'required|email|unique:users,email',
            'phone_number' => 'required|string|max:16|unique:users,phone_number',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'id_image' => 'nullable|image|mimes:jpg',
            'password'=>'required|confirmed|string|min:8',
            'password_confirmation'=>'required',
        ];
    }
}
