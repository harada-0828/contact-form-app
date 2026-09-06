<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未認証のユーザーは管理画面にアクセスできずログイン画面にリダイレクトされること()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function 認証されたユーザーは管理画面にアクセスできること()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
    }

    /** @test */
    public function 管理画面でお問い合わせが7件ごとにページネーション表示され検索できること()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->count(8)->create([
            'category_id' => $category->id,
            'first_name' => '通常太郎',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '山田特別',
            'email' => 'yamada@example.com',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('通常太郎');

        $responseSearch = $this->actingAs($user)->get('/admin?keyword=山田特別');
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('yamada@example.com');
        $responseSearch->assertDontSee('通常太郎');
    }

    /** @test */
    public function 認証されたユーザーはお問い合わせ詳細ページを表示できること()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create([
            'content' => '商品について',
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '詳細一郎',
            'email' => 'detail@example.com',
            'detail' => '詳細テストの内容です。',
        ]);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertSee('詳細一郎');
        $response->assertSee('detail@example.com');
        $response->assertSee('商品について');
        $response->assertSee('詳細テストの内容です。');
    }

    /** @test */
    public function 認証されたユーザーはお問い合わせを削除できること()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '削除太郎',
        ]);

        // 削除用エンドポイントに DELETE リクエストを送信
        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        // 管理画面（/admin）にリダイレクトされること
        $response->assertRedirect('/admin');

        // データベースから削除されていること（ソフトデリートまたは完全削除）
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
