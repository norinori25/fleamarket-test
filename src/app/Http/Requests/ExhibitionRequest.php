<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->category_ids && is_string($this->category_ids)) {
            $this->merge([
                'category_ids' => json_decode($this->category_ids, true)
            ]);
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'price' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png',
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'condition' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'category_ids.required' => 'カテゴリーを選択してください。',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '価格を入力してください',
            'price.integer' => '価格は数値で入力してください',
            'price.min' => '価格は0円以上で入力してください',
            'image.required' => '商品画像は必須です',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => 'アップロードできる画像は写真（JPEGまたはPNG）のみです',
        ];
    }
}
