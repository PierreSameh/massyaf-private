<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUnitRequest extends FormRequest
{
    protected $data = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images.*' => 'nullable|file|image|max:2048',
            'videos.*' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg|max:10240',
            
            // Fields for unit type, city, and compound/hotel
            'type' => ['required', 'in:unit,hotel'],
            'name' => ['required', 'string', 'max:255'],
            'unit_type_id' => ['required', 'exists:types,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'compound_id' => ['nullable', 'required_if:type,unit', 'exists:compounds,id'],
            'hotel_id' => ['nullable', 'required_if:type,hotel', 'exists:hotels,id'],
            'address' => ['nullable', 'required_if:type,unit', 'string', 'max:255'],
            'lat' => ['nullable', 'required_if:type,unit', 'string'],
            'lng' => ['nullable', 'required_if:type,unit', 'string'],
            'unit_number' => ['required', 'string', 'max:255'],
            'floors_count' => ['required', 'integer', 'min:1'],
            'elevator' => ['required', 'in:0,1'],
            'area' => ['required', 'integer', 'min:1'],
            'distance_unit_beach' => ['nullable', 'numeric', 'min:0'],
            'beach_unit_transportation' => ['nullable', 'in:car,walking'],
            'distance_unit_pool' => ['nullable', 'numeric', 'min:0'],
            'pool_unit_transportation' => ['nullable', 'in:car,walking'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['exists:amenities,id'],
            'room_count' => ['required', 'integer', 'min:1'],
            'toilet_count' => ['required', 'integer', 'min:1'],
            'reception' => ['nullable', 'array'],
            'kitchen' => ['nullable', 'array'],
            'description' => ['nullable', 'string'],
            'reservation_roles' => ['nullable', 'string'],
            'reservation_type' => ['required', 'in:direct,request'],
            'price' => ['required', 'numeric', 'min:0'],
            'insurance_amount' => ['required', 'numeric', 'min:0'],
            'max_individuals' => ['required', 'integer', 'min:1'],
            'youth_only' => ['required', 'in:0,1'],
            'min_reservation_days' => ['nullable', 'integer', 'min:1'],
            'deposit' => ['required', 'numeric', 'min:0', 'max:100'],
            'upon_arival_price' => ['required', 'numeric', 'min:0', 'max:100'],
            'weekend_prices' => ['required', 'in:0,1'],
            'min_weekend_period' => ['nullable', 'integer', 'min:1'],
            'weekend_price' => ['nullable', 'numeric', 'min:0'],
            
            // Rooms validation
            'rooms' => ['nullable', 'array'],
            'rooms.*.bed_count' => ['required', 'integer', 'min:1'],
            'rooms.*.bed_sizes' => ['nullable', 'array'],
            'rooms.*.amenities' => ['nullable', 'array'],
            
            // Available Dates validation
            'available_dates' => ['nullable', 'array'],
            'available_dates.*.from' => ['required', 'date'],
            'available_dates.*.to' => ['required', 'date', 'after_or_equal:available_dates.*.from'],
            
            // Cancel Policies validation
            'cancel_policies' => ['nullable', 'array'],
            'cancel_policies.*.days' => ['required', 'integer', 'min:0'],
            'cancel_policies.*.penalty' => ['required', 'numeric', 'min:0', 'max:100'],
            
            // Additional Fees validation
            'additional_fees' => ['nullable', 'array'],
            'additional_fees.*.fees' => ['required', 'string', 'max:255'],
            'additional_fees.*.amount' => ['required', 'numeric', 'min:0'],
            
            // Long Term Reservations validation
            'long_term_reservations' => ['nullable', 'array'],
            'long_term_reservations.*.more_than_days' => ['required', 'integer', 'min:1'],
            'long_term_reservations.*.sale_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            
            // Sales validation
            'sales' => ['nullable', 'array'],
            'sales.*.from' => ['required', 'date'],
            'sales.*.to' => ['required', 'date', 'after_or_equal:sales.*.from'],
            'sales.*.sale_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            
            // Special Reservation Times validation
            'special_reservation_times' => ['nullable', 'array'],
            'special_reservation_times.*.from' => ['required', 'date'],
            'special_reservation_times.*.to' => ['required', 'date', 'after_or_equal:special_reservation_times.*.from'],
            'special_reservation_times.*.price' => ['required', 'numeric', 'min:0'],
            'special_reservation_times.*.min_reservation_period' => ['required', 'integer', 'min:1'],
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}