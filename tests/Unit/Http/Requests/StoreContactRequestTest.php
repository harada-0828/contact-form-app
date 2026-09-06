<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function validateData(array $data)
    {
        $request = new StoreContactRequest;

        return Validator::make($data, $request->rules(), $request->messages());
    }

    /** @test */
    public function valid_data_passes_validation()
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせテスト内容です。',
        ];

        $validator = $this->validateData($data);
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function required_fields_are_required()
    {
        $validator = $this->validateData([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('first_name', $validator->errors()->messages());
        $this->assertArrayHasKey('last_name', $validator->errors()->messages());
        $this->assertArrayHasKey('gender', $validator->errors()->messages());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
        $this->assertArrayHasKey('tel', $validator->errors()->messages());
        $this->assertArrayHasKey('address', $validator->errors()->messages());
        $this->assertArrayHasKey('category_id', $validator->errors()->messages());
        $this->assertArrayHasKey('detail', $validator->errors()->messages());
    }

    /** @test */
    public function tel_format_must_be_valid()
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '090-1234-5678', // ハイフンありは不正とするテスト
            'address' => '東京都渋谷区',
            'category_id' => $category->id,
            'detail' => 'テスト内容',
        ];

        $validator = $this->validateData($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tel', $validator->errors()->messages());
    }
}
