<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'required|string',
        'topic' => 'required|string',
        'text' => 'required|string',
        'attachment' => 'nullable|file|mimes:jpg,png,pdf,txt|max:2048',
    ];
}
}
