<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_category_and_can_sync_tags()
    {
        // 1. カテゴリを作成し、それに紐づくお問い合わせを作成する
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        // 2. お問い合わせが正しくカテゴリに属している（belongsTo）ことを検証
        $this->assertInstanceOf(Category::class, $contact->category);
        $this->assertEquals($category->id, $contact->category->id);

        // 3. タグを3つ作成する
        $tags = Tag::factory()->count(3)->create();

        // 4. お問い合わせに複数のタグを紐付ける（syncメソッドを使用）
        $tagIds = $tags->take(2)->pluck('id')->toArray();
        $contact->tags()->sync($tagIds);

        // 5. 紐付けたタグが正しく2件取得できることを検証
        $this->assertCount(2, $contact->tags);
        foreach ($tagIds as $tagId) {
            $this->assertTrue($contact->tags->contains($tagId));
        }
    }
}