<?php

namespace Tests\Unit\Http\Requests\Api\V1;

use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function validate(array $data)
    {
        $request = new IndexContactRequest;

        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_valid_parameters_pass_validation()
    {
        $category = Category::factory()->create();

        $data = [
            'keyword' => 'テスト',
            'gender' => '1',
            'category_id' => $category->id,
            'date' => '2026-06-06',
        ];

        $validator = $this->validate($data);
        $this->assertFalse($validator->fails());
    }

    public function test_invalid_gender_fails_validation()
    {
        $data = [
            'gender' => '99', // 不正な性別値
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }

    public function test_non_existent_category_id_fails_validation()
    {
        $data = [
            'category_id' => 99999, // 存在しないカテゴリID
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
    }

    public function test_invalid_date_format_fails_validation()
    {
        $data = [
            'date' => 'invalid-date', // 不正な日付形式
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }
}
