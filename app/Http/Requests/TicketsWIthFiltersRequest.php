<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TicketsWIthFiltersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customerId' => 'nullable|integer|exists:customers,id',
            'status' => 'nullable|string|in:new,processing,done,all',
            'date' => 'nullable|date',
            'email' => 'nullable|email|exists:customers,email',
            'phone' => 'nullable|string|exists:customers,phone',
        ];
    }
}
