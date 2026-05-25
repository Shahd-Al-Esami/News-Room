<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
        'file_name' => fake()->word() . '.' . fake()->fileExtension(),
        'file_path' => 'uploads/' . fake()->uuid() . '.pdf',
        'file_type' => fake()->randomElement(['application/pdf', 'image/png', 'image/jpeg']),
        'file_size' => fake()->numberBetween(1000, 5000000), // بين 1KB و 5MB
        'attachable_id' => null,
        'attachable_type' => null,
    ];
    }
}
