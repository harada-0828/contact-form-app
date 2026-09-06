<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_export_contacts()
    {
        $response = $this->get('/contacts/export');

        // 未認証の場合はログイン画面等へリダイレクトされること
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_export_contacts()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        // ログインしてエクスポートを実行
        $response = $this->actingAs($user)->get('/contacts/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=contacts_', $response->headers->get('Content-Disposition'));
    }

    public function test_export_with_filter_conditions()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // 検索にヒットするデータ
        Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'yamada@example.com',
            'category_id' => $category->id,
        ]);

        // ヒットしないデータ
        Contact::factory()->create([
            'first_name' => '田中',
            'last_name' => '次郎',
            'email' => 'tanaka@example.com',
            'category_id' => $category->id,
        ]);

        // キーワード「山田」でフィルタしてエクスポート
        $response = $this->actingAs($user)->get('/contacts/export?keyword=山田');

        $response->assertStatus(200);
        
        // StreamedResponseの場合は streamedContent() を使用する
        $content = $response->streamedContent();

        // CSV内に「山田」が含まれており、「田中」が含まれていないことを確認
        $this->assertStringContainsString('山田', $content);
        $this->assertStringNotContainsString('田中', $content);
    }
}