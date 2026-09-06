<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['1', '2', '3'])], // 性別の選択肢に合わせたバリデーション（例: 1, 2, 3など）
            'category_id' => ['nullable', 'exists:categories,id'],
            'date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'gender.in' => '性別の値が不正です',
        ];
    }
}
