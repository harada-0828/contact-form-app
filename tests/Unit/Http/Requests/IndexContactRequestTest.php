<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category; // 追加
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function valid_search_parameters_pass()
    {
        // 存在するカテゴリをあらかじめ作成する
        $category = Category::factory()->create();

        $request = new IndexContactRequest();
        $data = [
            'keyword' => '山田',
            'gender' => '1',
            'category_id' => $category->id, // 作成したカテゴリのIDを指定
            'date' => '2026-09-04',
        ];

        $validator = Validator::make($data, $request->rules(), $request->messages());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function invalid_gender_is_rejected()
    {
        $request = new IndexContactRequest();
        $data = [
            'gender' => '99', // 不正な性別値
        ];

        $validator = Validator::make($data, $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->messages());
    }
}