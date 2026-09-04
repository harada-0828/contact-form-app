<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_keeping_own_name_but_rejects_duplicates()
    {
        $tag1 = Tag::factory()->create(['name' => 'PHP']);
        Tag::factory()->create(['name' => 'JavaScript']);

        $request = new UpdateTagRequest();
        
        // FormRequestに対して直接ルートパラメータ（プレースホルダー）を設定する
        $request->setRouteResolver(function () use ($tag1) {
            $route = new \Illuminate\Routing\Route('PUT', 'admin/tags/{tag}', []);
            $route->parameters['tag'] = $tag1->id; // ここで直接IDを紐付ける
            return $route;
        });

        // 1. 自身の名前（PHP）のままでの更新は許可されること
        $validator1 = Validator::make(['name' => 'PHP'], $request->rules(), $request->messages());
        $this->assertTrue($validator1->passes());

        // 2. 他のタグが使っている名前（JavaScript）への変更は拒否されること
        $validator2 = Validator::make(['name' => 'JavaScript'], $request->rules(), $request->messages());
        $this->assertTrue($validator2->fails());
        $this->assertArrayHasKey('name', $validator2->errors()->messages());
    }
}