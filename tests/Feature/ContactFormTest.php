<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ入力画面が表示されること()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function 必須項目が未入力の場合に確認画面でエラーになること()
    {
        $response = $this->post('/contacts/confirm', []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'tel',
            'address',
            'category_id',
            'gender',
            'detail',
        ]);
    }

    /** @test */
    public function 正常なデータを送信した場合にデータベースに保存されサンクスページにリダイレクトされること()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $formData = [
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'gender' => '1',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
            'detail' => 'テストのお問い合わせ内容です。',
        ];

        // 直接 /contacts へPOST
        $response = $this->post('/contacts', $formData);

        // データベースに保存されていること
        $this->assertDatabaseHas('contacts', [
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'email' => 'test@example.com',
            'tel' => '09012345678',
        ]);

        // サンクスページへリダイレクトされること
        $response->assertRedirect('/thanks');
    }
}
