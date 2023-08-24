<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->title(),
            'description' => $this->faker->paragraph(),
            'language' => 'en',
            'license' => null,
        ];
    }

    /**
     * Indicate that the video is published.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => now(),
            ];
        });
    }
}
