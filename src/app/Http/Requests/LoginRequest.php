<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // バリデーションエラー（未入力など）の場合
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function failedAuthorization()
    {
        // 認証失敗時（メール・パスワードが一致しない場合）
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }
}
