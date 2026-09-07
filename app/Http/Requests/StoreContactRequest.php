<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $tel1 = $this->input('tel1');
        $tel2 = $this->input('tel2');
        $tel3 = $this->input('tel3');

        if (! empty($tel1) || ! empty($tel2) || ! empty($tel3)) {
            $this->merge([
                'tel' => $tel1.$tel2.$tel3,
            ]);
        } elseif ($this->has('tel')) {
            $rawTel = $this->input('tel');
            if (is_string($rawTel)) {

                $cleanTel = str_replace('-', '', $rawTel);
                $this->merge([
                    'tel' => $cleanTel,
                ]);
            }
        }
    }

    public function rules(): array
    {
        return
        [
            'category_id' => ['required', 'exists:categories,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'integer', 'in:1,2,3'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'tel' => ['required', 'regex:/^[0-9]{10,11}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'detail' => ['required', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return
        [
            'first_name.required' => '姓を入力してください',
            'first_name.max' => '姓は255文字以内で入力してください',
            'last_name.required' => '名を入力してください',
            'last_name.max' => '名は255文字以内で入力してください',
            'gender.required' => '性別を選択してください',
            'gender.in' => '性別の値が不正です',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'email.max' => 'メールアドレスは255文字以内で入力してください',
            'tel.required' => '電話番号を入力してください',
            'tel.regex' => '電話番号は10桁または11桁の半角数字で入力してください',
            'address.required' => '住所を入力してください',
            'address.max' => '住所は255文字以内で入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'category_id.exists' => '選択されたお問い合わせの種類が不正です',
            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max' => 'お問い合わせ内容は120文字以内で入力してください',
        ];
    }
}
