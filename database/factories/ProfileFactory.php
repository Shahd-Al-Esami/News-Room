<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // سيقوم بإنشاء مستخدم جديد وربطه تلقائياً إذا لم نمرر user_id جاهز
            'user_id' => User::factory(),
            'bio' => fake()->paragraph(),
            'phone' => fake()->optional()->phoneNumber(), // بعض الملفات ستحتوي على رقم هاتف وبعضها لا
            'avatar' => 'avatars/' . fake()->uuid() . '.jpg', // مسار وهمي للصورة الشخصية
            'activity_score' => function () {
                $user = User::factory()->create();
                if ($user->role === 'writer') {
                    return fake()->randomFloat(2, 0, 100);
                }
                return null;
            },
        ];
    }
}
