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
            'payment_method' => 'required',
            'postal_code'    => 'required',
            'address'        => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'postal_code.required'    => '郵便番号を入力してください',
            'address.required'        => '住所を入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->postal_code || !$this->address) {
                $validator->errors()->add('shipping_address', '配送先を選択してください');
            }
        });
    }

}

