<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function valid_data_passes_validation()
    {
        $request = new StoreTagRequest();
        $validator = Validator::make(['name' => 'PHP'], $request->rules(), $request->messages());
        
        $this->assertTrue($validator->passes(), 'Validation failed: ' . json_encode($validator->errors()->all()));
    }

    /** @test */
    public function name_is_required()
    {
        $request = new StoreTagRequest();
        $validator = Validator::make(['name' => ''], $request->rules(), $request->messages());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    /** @test */
    public function name_must_not_exceed_50_characters()
    {
        $request = new StoreTagRequest();
        $validator = Validator::make(['name' => str_repeat('あ', 51)], $request->rules(), $request->messages());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    /** @test */
    public function name_must_be_unique()
    {
        Tag::factory()->create(['name' => 'Laravel']);

        $request = new StoreTagRequest();
        $validator = Validator::make(['name' => 'Laravel'], $request->rules(), $request->messages());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }
}