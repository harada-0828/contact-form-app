<?php

namespace Tests\Unit\Http\Requests\Api\V1;

use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateContactRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function validate(array $data)
    {
        $request = new UpdateContactRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_valid_parameters_pass_validation()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'first_name' => '次郎',
            'last_name' => '佐藤',
            'gender' => 2,
            'email' => 'update@example.com',
            'tel' => '08098765432',
            'address' => '大阪府大阪市',
            'building' => 'テストビル202',
            'category_id' => $category->id,
            'detail' => '更新用のお問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $validator = $this->validate($data);
        $this->assertFalse($validator->fails());
    }

    public function test_required_fields_fail_validation()
    {
        $validator = $this->validate([]);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('first_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('last_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('tel', $validator->errors()->toArray());
        $this->assertArrayHasKey('address', $validator->errors()->toArray());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('detail', $validator->errors()->toArray());
    }

    public function test_invalid_gender_fails_validation()
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => '次郎',
            'last_name' => '佐藤',
            'gender' => 9, // 不正な性別値
            'email' => 'update@example.com',
            'tel' => '08098765432',
            'address' => '大阪府大阪市',
            'category_id' => $category->id,
            'detail' => 'テスト',
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }

    public function test_invalid_tag_id_fails_validation()
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => '次郎',
            'last_name' => '佐藤',
            'gender' => 2,
            'email' => 'update@example.com',
            'tel' => '08098765432',
            'address' => '大阪府大阪市',
            'category_id' => $category->id,
            'detail' => 'テスト',
            'tag_ids' => [99999], // 存在しないタグID
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tag_ids.0', $validator->errors()->toArray());
    }
}