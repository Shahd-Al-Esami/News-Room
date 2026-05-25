<?php

namespace Database\Factories;

use App\Enums\ArticleCategory;
use App\Enums\ArticleStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        // سيقوم لارافيل بإنشاء مستخدم تلقائياً وربطه بالمقال
        'writer_id' => User::factory(),
        'title' => fake()->sentence(),
        'content' => fake()->paragraphs(3, true),
        'status' => fake()->randomElement(array_column(ArticleStatus::cases(),'value')),
        'published_at' => fake()->optional()->dateTime(),
        'slug' => fake()->unique()->slug(),
        'category' => fake()->randomElement(array_column(ArticleCategory::cases(),'value')),

    ];
    }
}
