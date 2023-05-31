<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testOnStoreInvalidationNoneDataInAllAttributes()
    {
        $attributes = [];

        $response = $this->json(
            'POST',
            route('genres.store'),
            $attributes
        );

        $this->assertInvalidationNameRequired($response);
    }

    public function testOnStoreInvalidationExceededSizeDataInNameAttribute()
    {
        $attributes = ['name' => str_repeat('x', 256)];

        $response = $this->json(
            'POST',
            route('genres.store'),
            $attributes
        );

        $this->assertInvalidationNameMax($response);
    }

    public function testOnStoreInvalidationDataInIsActiveAttribute()
    {
        $attributes = ['name' => str_repeat('x', 255), 'is_active' => 'string'];

        $response = $this->json(
            'POST',
            route('genres.store'),
            $attributes
        );

        $this->assertInvalidationIsActiveBoolean($response);
    }

    public function testOnUpdateInvalidationNoneDataInAllAttributes()
    {
        $genre = factory(Genre::class)->create();
        $attributes = [];

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            $attributes
        );

        $this->assertInvalidationNameRequired($response);
    }

    public function testOnUpdateInvalidationExceededSizeDataInNameAttribute()
    {
        $genre = factory(Genre::class)->create();
        $attributes = ['name' => str_repeat('x', 256)];

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            $attributes
        );

        $this->assertInvalidationNameMax($response);
    }

    public function testOnUpdateInvalidationDataInIsActiveAttribute()
    {
        $genre = factory(Genre::class)->create();
        $attributes = ['name' => str_repeat('x', 255), 'is_active' => 'string'];

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            $attributes
        );

        $this->assertInvalidationIsActiveBoolean($response);
    }

    private function assertInvalidationNameRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                __('validation.required', ['attribute' => 'name'])
            ]);
    }

    private function assertInvalidationNameMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                __('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    private function assertInvalidationIsActiveBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                __('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function testOnStoreWithOnlyNameAttribute()
    {
        $attributes = ['name' => 'genre name'];

        $response = $this->json(
            'POST',
            route('genres.store'),
            $attributes
        );
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));
    }

    public function testOnStoreWithAllAttributes()
    {
        $attributes = [
            'name' => 'genre name',
            'is_active' => false
        ];

        $response = $this->json(
            'POST',
            route('genres.store'),
            $attributes
        );

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'is_active' => false
            ]);
    }

    public function testOnUpdateWithAllAttributes()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);
        $attributes = [
            'name' => 'genre name',
            'is_active' => true
        ];

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            $attributes
        );
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true
            ]);
    }
}
