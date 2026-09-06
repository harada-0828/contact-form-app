<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_contacts_list()
    {
        $category = Category::factory()->create();
        Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'category', // category_id ではなく category
                        'first_name',
                        'last_name',
                        'gender',
                        'email',
                        'tel',
                        'address',
                        'building',
                        'detail',
                        'tags',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_index_validation_fails_with_invalid_parameters()
    {
        $response = $this->getJson('/api/v1/contacts?gender=99&date=invalid-date');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gender', 'date']);
    }

    public function test_can_get_contact_detail()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $contact->id,
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                ],
            ]);
    }

    public function test_returns_404_when_contact_not_found()
    {
        $response = $this->getJson('/api/v1/contacts/99999');

        $response->assertStatus(404);
    }

    public function test_can_create_contact()
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'create_test@example.com',
            'tel' => '09012345678',
            'address' => '東京都千代田区',
            'building' => 'テストビル1階',
            'category_id' => $category->id,
            'detail' => 'APIからの新規作成テストです。',
        ];

        $response = $this->postJson('/api/v1/contacts', $data);

        // 作成成功時は201 Created
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'テスト',
                    'last_name' => '太郎',
                    'email' => 'create_test@example.com',
                ],
            ]);

        // データベースに保存されているか確認
        $this->assertDatabaseHas('contacts', [
            'email' => 'create_test@example.com',
        ]);
    }

    public function test_create_validation_fails_with_invalid_parameters()
    {
        // 必須項目が空の場合、422 Unprocessable Entity
        $response = $this->postJson('/api/v1/contacts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'category_id',
                'detail',
            ]);
    }

    public function test_can_update_contact()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '変更前',
        ]);

        $newCategory = Category::factory()->create();

        $data = [
            'first_name' => '変更後',
            'last_name' => $contact->last_name,
            'gender' => $contact->gender,
            'email' => $contact->email,
            'tel' => $contact->tel,
            'address' => $contact->address,
            'building' => $contact->building,
            'category_id' => $newCategory->id,
            'detail' => $contact->detail,
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => '変更後',
                ],
            ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => '変更後',
            'category_id' => $newCategory->id,
        ]);
    }

    public function test_update_returns_404_when_contact_not_found()
    {
        $response = $this->putJson('/api/v1/contacts/99999', [
            'first_name' => 'テスト',
        ]);

        $response->assertStatus(404);
    }

    public function test_update_validation_fails_with_invalid_parameters()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        // 必須項目を欠いた不正なデータで更新
        $response = $this->putJson("/api/v1/contacts/{$contact->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'category_id',
                'detail',
            ]);
    }

    public function test_can_delete_contact()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");

        // 削除成功時は204 No Content（または200の場合もあるため適宜、一般的には204）
        $response->assertStatus(204);

        // データベースから削除されている（またはソフトデリートされている）ことを確認
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_delete_returns_404_when_contact_not_found()
    {
        $response = $this->deleteJson('/api/v1/contacts/99999');

        $response->assertStatus(404);
    }
}
