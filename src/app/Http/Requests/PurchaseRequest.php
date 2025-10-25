<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:konbini,card'],
            'shipping_address_id' => ['required', 'integer', 'exists:addresses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in' => '正しい支払い方法を選択してください。',
            'shipping_address_id.required' => '配送先を選択してください。',
            'shipping_address_id.exists' => '選択した配送先が存在しません。',
        ];
    }
}
