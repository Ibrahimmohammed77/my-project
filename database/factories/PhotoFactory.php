<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        return [
            'album_id' => Album::factory(),
            'original_name' => $this->faker->word . '.jpg',
            'stored_name' => $this->faker->uuid . '.jpg',
            'file_path' => 'public/photos/' . $this->faker->uuid . '.jpg',
            'file_size' => $this->faker->numberBetween(1000, 5000000),
            'mime_type' => 'image/jpeg',
            'width' => 1920,
            'height' => 1080,
            'review_status' => Photo::STATUS_PENDING,
            'is_hidden' => false,
            'is_archived' => false,
        ];
    }
}
