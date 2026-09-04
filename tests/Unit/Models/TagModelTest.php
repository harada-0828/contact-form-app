<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_contacts_through_pivot()
    {
        // 1. タグを1つ作成する
        $tag = Tag::factory()->create();

        // 2. お問い合わせを2件作成する
        $contacts = Contact::factory()->count(2)->create();

        // 3. 中間テーブルを介して、両方のお問い合わせをこのタグに紐付ける
        $tag->contacts()->attach($contacts->pluck('id'));

        // 4. タグから紐づくお問い合わせが2件正しく取得できることを検証
        $this->assertCount(2, $tag->contacts);
        $this->assertInstanceOf(Contact::class, $tag->contacts->first());
    }
}