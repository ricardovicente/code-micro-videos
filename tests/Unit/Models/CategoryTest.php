<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals(
            $fillable,
            $this->category->getFillable()
        );
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $categoryGetDates = $this->category->getDates();
        foreach ($dates as $date) {
            $this->assertContains($date, $categoryGetDates);
        }
        $this->assertCount(count($dates), $categoryGetDates);
    }

    public function testUsesTraits()
    {
        $expected_traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $traits = class_uses(Category::class);
        $categoryTraits = array_values($traits);
        $this->assertEquals($expected_traits, $categoryTraits);
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->category->incrementing);
    }
}
