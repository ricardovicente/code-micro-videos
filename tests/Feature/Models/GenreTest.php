<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 3)->create();
        $genres = Genre::all();
        $this->assertCount(3, $genres);
        $genreKeys = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $genreKeys
        );
    }

    public function testCreateUuidAttribute()
    {
        $genre = Genre::create(['name' => 'Test One']);
        $genre->refresh();
        $this->assertIsValidUuid($genre->id);
        $this->assertEquals('Test One', $genre->name);
        $this->assertTrue($genre->is_active);
    }

    public function testCreatePassingOnlyNameAttribute()
    {
        $genre = Genre::create(['name' => 'Test One']);
        $genre->refresh();
        $this->assertEquals('Test One', $genre->name);
        $this->assertTrue($genre->is_active);
    }

    public function testCreatePassingNameAndIsActiveAttributes()
    {
        $genre = Genre::create([
            'name' => 'Test One',
            'is_active' => false
        ]);
        $genre->refresh();
        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'Test One',
            'is_active' => true
        ]);
        $genre->refresh();
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'name_updated',
            'is_active' => true
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        $genre = factory(Genre::class)->create();

        $genre->delete();

        $this->assertNull(Genre::find($genre->id));
        $this->assertCount(1, $genre->withTrashed()->get());
    }

    public function testRestoreDelete()
    {
        $genre = factory(Genre::class)->create();
        $genre->delete();

        $genre->restore();

        $this->assertNotNull(Genre::find($genre->id));
    }
}
