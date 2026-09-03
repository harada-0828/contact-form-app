<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');
        $categories = Category::all();
        $tags = Tag::all();

        for ($i = 0; $i < 20; $i++) {
            $contact = Contact::create([
                'category_id' => $categories->random()->id,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->safeEmail,
                'tel' => $faker->numerify('00000000000'),
                'address' => $faker->address,
                'building' => $faker->optional(0.7)->secondaryAddress,
                'detail' => mb_substr($faker->realText(100), 0, 120),
            ]);

            $randomTags = $tags->random(rand(1, 3))->pluck('id');
            $contact->tags()->attach($randomTags);
        }
    }
}
