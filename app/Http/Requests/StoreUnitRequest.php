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

    protected function prepareForValidation()
    {
        if (is_string($this->input('data'))) {
            $this->merge([
                'data' => json_decode($this->input('data'), true)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'images.*' => 'nullable|file|image|max:2048',
            'videos.*' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg|max:10240',
            
            // Fields for unit type, city, and compound/hotel
            'data' => ['required', 'array'],
            'data.owner_id' => ['required'],
            'data.type' => ['required', 'in:unit,hotel'],
            'data.unit_type_id' => ['required', 'exists:types,id'],
            'data.city_id' => ['required', 'exists:cities,id'],
            'data.compound_id' => ['nullable', 'required_if:data.type,unit', 'exists:compounds,id'],
            'data.hotel_id' => ['nullable', 'required_if:data.type,hotel', 'exists:hotels,id'],
            'data.address' => ['nullable', 'required_if:data.type,unit', 'string', 'max:255'],
            'data.lat' => ['nullable', 'required_if:data.type,unit', 'string'],
            'data.lng' => ['nullable', 'required_if:data.type,unit', 'string'],
            'data.unit_number' => ['required', 'string', 'max:255'],
            'data.floors_count' => ['required', 'integer', 'min:1'],
            'data.elevator' => ['required', 'boolean'],
            'data.area' => ['required', 'integer', 'min:1'],
            'data.distance_unit_beach' => ['nullable', 'numeric', 'min:0'],
            'data.beach_unit_transportation' => ['nullable', 'in:car,walking'],
            'data.distance_unit_pool' => ['nullable', 'numeric', 'min:0'],
            'data.pool_unit_transportation' => ['nullable', 'in:car,walking'],
            'data.room_count' => ['required', 'integer', 'min:1'],
            'data.toilet_count' => ['required', 'integer', 'min:1'],
            'data.description' => ['nullable', 'string'],
            'data.reservation_roles' => ['nullable', 'string'],
            'data.reservation_type' => ['required', 'in:direct,request'],
            'data.price' => ['required', 'numeric', 'min:0'],
            'data.insurance_amount' => ['required', 'numeric', 'min:0'],
            'data.max_individuals' => ['required', 'integer', 'min:1'],
            'data.youth_only' => ['required', 'boolean'],
            'data.min_reservation_days' => ['nullable', 'integer', 'min:1'],
            'data.deposit' => ['required', 'numeric', 'min:0'],
            'data.upon_arival_price' => ['required', 'numeric', 'min:0'],
            'data.weekend_prices' => ['required', 'boolean'],
            'data.min_weekend_period' => ['nullable', 'integer', 'min:1'],
            'data.weekend_price' => ['nullable', 'numeric', 'min:0'],
            
            // Rooms validation
            'data.rooms' => ['nullable', 'array'],
            'data.rooms.*.bed_count' => ['required', 'integer', 'min:1'],
            'data.rooms.*.bed_sizes' => ['nullable', 'array'],
            'data.rooms.*.amenities' => ['nullable', 'array'],
            
            // Available Dates validation
            'data.available_dates' => ['nullable', 'array'],
            'data.available_dates.*.from' => ['required', 'date'],
            'data.available_dates.*.to' => ['required', 'date', 'after_or_equal:data.available_dates.*.from'],
            
            // Cancel Policies validation
            'data.cancel_policies' => ['nullable', 'array'],
            'data.cancel_policies.*.days' => ['required', 'integer', 'min:0'],
            'data.cancel_policies.*.penalty' => ['required', 'numeric', 'min:0'],
            
            // Additional Fees validation
            'data.additional_fees' => ['nullable', 'array'],
            'data.additional_fees.*.fees' => ['required', 'string', 'max:255'],
            'data.additional_fees.*.amount' => ['required', 'numeric', 'min:0'],
            
            // Long Term Reservations validation
            'data.long_term_reservations' => ['nullable', 'array'],
            'data.long_term_reservations.*.more_than_days' => ['required', 'integer', 'min:1'],
            'data.long_term_reservations.*.sale_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            
            // Sales validation
            'data.sales' => ['nullable', 'array'],
            'data.sales.*.from' => ['required', 'date'],
            'data.sales.*.to' => ['required', 'date', 'after_or_equal:data.sales.*.from'],
            'data.sales.*.sale_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            
            // Special Reservation Times validation
            'data.special_reservation_times' => ['nullable', 'array'],
            'data.special_reservation_times.*.from' => ['required', 'date'],
            'data.special_reservation_times.*.to' => ['required', 'date', 'after_or_equal:data.special_reservation_times.*.from'],
            'data.special_reservation_times.*.price' => ['required', 'numeric', 'min:0'],
            'data.special_reservation_times.*.min_reservation_period' => ['required', 'integer', 'min:1'],
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}