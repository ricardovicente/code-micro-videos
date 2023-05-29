<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class GenreTest extends TestCase
{
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals(
            $fillable,
            $this->genre->getFillable()
        );
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $genreGetDates = $this->genre->getDates();
        foreach ($dates as $date) {
            $this->assertContains($date, $genreGetDates);
        }
        $this->assertCount(count($dates), $genreGetDates);
    }

    public function testUsesTraits()
    {
        $expected_traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $traits = class_uses(Genre::class);
        $genreTraits = array_values($traits);
        $this->assertEquals($expected_traits, $genreTraits);
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->genre->incrementing);
    }
}
