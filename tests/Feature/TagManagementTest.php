<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未認証のユーザーはタグの操作ができずログイン画面にリダイレクトされること()
    {
        $tag = Tag::factory()->create();

        // 1. タグ作成 (POST /admin/tags)
        $response = $this->post('/admin/tags', ['name' => '新しいタグ']);
        $response->assertRedirect('/login');

        // 2. 編集画面表示 (GET /admin/tags/{tag}/edit)
        $response = $this->get("/admin/tags/{$tag->id}/edit");
        $response->assertRedirect('/login');

        // 3. タグ更新 (PUT /admin/tags/{tag})
        $response = $this->put("/admin/tags/{$tag->id}", ['name' => '更新タグ']);
        $response->assertRedirect('/login');

        // 4. タグ削除 (DELETE /admin/tags/{tag})
        $response = $this->delete("/admin/tags/{$tag->id}");
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 認証されたユーザーはタグを作成して管理画面にリダイレクトされること()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/tags', [
            'name' => 'テストタグ',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'name' => 'テストタグ',
        ]);
    }

    /** @test */
    public function 認証されたユーザーはタグ編集画面を表示できること()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '編集前タグ']);

        $response = $this->actingAs($user)->get("/admin/tags/{$tag->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('編集前タグ');
    }

    /** @test */
    public function 認証されたユーザーはタグを更新して管理画面にリダイレクトされること()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '旧タグ名']);

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", [
            'name' => '新タグ名',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '新タグ名',
        ]);
    }

    /** @test */
    public function 認証されたユーザーはタグを削除して管理画面にリダイレクトされること()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '削除対象タグ']);

        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}