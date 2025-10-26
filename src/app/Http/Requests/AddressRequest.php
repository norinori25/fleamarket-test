<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'], // ハイフンあり8桁
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'], // 任意
        ];
    }

    public function messages(): array
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフンありの7桁（例: 123-4567）で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
