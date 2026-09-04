<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_contacts()
    {
        // 1. カテゴリを1つ作成する
        $category = Category::factory()->create();

        // 2. そのカテゴリに紐づくお問い合わせを2件作成する
        Contact::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);

        // 3. カテゴリから紐づくお問い合わせを取得し、件数が2件であること、かつCollectionであることを検証する
        $this->assertCount(2, $category->contacts);
        $this->assertInstanceOf(Contact::class, $category->contacts->first());
    }
}
