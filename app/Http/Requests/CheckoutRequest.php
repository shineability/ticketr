<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1|max:10',
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.*' => 'Please fill in a valid email address',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
