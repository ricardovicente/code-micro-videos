<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testOnStoreInvalidationNoneDataInAllAttributes()
    {
        $attributes = [];

        $response = $this->json(
            'POST',
            route('categories.store'),
            $attributes
        );

        $this->assertInvalidationNameRequired($response);
    }

    public function testOnStoreInvalidationExceededSizeDataInNameAttribute()
    {
        $attributes = ['name' => str_repeat('x', 256)];

        $response = $this->json(
            'POST',
            route('categories.store'),
            $attributes
        );

        $this->assertInvalidationNameMax($response);
    }

    public function testOnStoreInvalidationDataInIsActiveAttribute()
    {
        $attributes = ['name' => str_repeat('x', 255), 'is_active' => 'string'];

        $response = $this->json(
            'POST',
            route('categories.store'),
            $attributes
        );

        $this->assertInvalidationIsActiveBoolean($response);
    }

    public function testOnUpdateInvalidationNoneDataInAllAttributes()
    {
        $category = factory(Category::class)->create();
        $attributes = [];

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            $attributes
        );

        $this->assertInvalidationNameRequired($response);
    }

    public function testOnUpdateInvalidationExceededSizeDataInNameAttribute()
    {
        $category = factory(Category::class)->create();
        $attributes = ['name' => str_repeat('x', 256)];

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            $attributes
        );

        $this->assertInvalidationNameMax($response);
    }

    public function testOnUpdateInvalidationDataInIsActiveAttribute()
    {
        $category = factory(Category::class)->create();
        $attributes = ['name' => str_repeat('x', 255), 'is_active' => 'string'];

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
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
        $attributes = ['name' => 'category name'];

        $response = $this->json(
            'POST',
            route('categories.store'),
            $attributes
        );
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertNull($response->json('description'));
        $this->assertTrue($response->json('is_active'));
    }

    public function testOnStoreWithAllAttributes()
    {
        $attributes = [
            'name' => 'category name',
            'description' => 'category description',
            'is_active' => false
        ];

        $response = $this->json(
            'POST',
            route('categories.store'),
            $attributes
        );

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'description' => 'category description',
                'is_active' => false
            ]);
    }

    public function testOnUpdateWithAllAttributes()
    {
        $category = factory(Category::class)->create([
            'description' => 'category description',
            'is_active' => false
        ]);
        $attributes = [
            'name' => 'category name',
            'description' => 'new category description',
            'is_active' => true
        ];

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            $attributes
        );
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => 'new category description',
                'is_active' => true
            ]);
    }

    public function testOnDelete()
    {
        $category = factory(Category::class)->create();

        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => $category->id])
        );

        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }

    public function testOnDeleteWithRestore()
    {
        $category = factory(Category::class)->create();

        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => $category->id])
        );

        $response->assertStatus(204);

        $category->restore();

        $this->assertNotNull(Category::find($category->id));
    }
}
