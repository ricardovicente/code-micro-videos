<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $categoryKeys
        );
    }

    public function testCreateUuidAttribute()
    {
        $category = Category::create(['name' => 'Test One']);
        $category->refresh();
        $this->assertIsValidUuid($category->id);
        $this->assertEquals('Test One', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
    }

    public function testCreatePassingOnlyNameAttribute()
    {
        $category = Category::create(['name' => 'Test One']);
        $category->refresh();
        $this->assertEquals('Test One', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
    }

    public function testCreatePassingNameAndDescriptionAttributes()
    {
        $category = Category::create([
            'name' => 'Test One',
            'description' => null
        ]);
        $category->refresh();
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Test One',
            'description' => 'description_content'
        ]);
        $category->refresh();
        $this->assertEquals('description_content', $category->description);
    }

    public function testCreatePassingNameAndIsActiveAttributes()
    {
        $category = Category::create([
            'name' => 'Test One',
            'is_active' => false
        ]);
        $category->refresh();
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'Test One',
            'is_active' => true
        ]);
        $category->refresh();
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'description_content',
            'is_active' => false
        ]);

        $data = [
            'name' => 'name_updated',
            'description' => 'description_content_updated',
            'is_active' => true
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create();

        $category->delete();
        $this->assertNull(Category::find($category->id));
        $this->assertCount(1, $category->withTrashed()->get());
    }

    public function testRestoreDelete()
    {
        $category = factory(Category::class)->create();
        $category->delete();
        $category->restore();
        $this->assertNotNull(Category::find($category->id));
    }
}
