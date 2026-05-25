<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    //     User::factory(10)->create()
    // ->each(fn ($user) => Profile::factory()->create(['user_id' => $user->id]));

    $admin = User::factory()->create([
            'first_name' => 'TechNova Admin',
            'last_name' => 'User',
            'email' => 'admin@technova.com',
            'password' => 'adminpassword',
            'role' => 'admin'
        ]);
Profile::factory()->create(['user_id' => $admin->id]); // Intermediate




   $reader = User::factory()->create([
            'first_name' => 'TechNova reader',
            'last_name' => 'User',
            'email' => 'reader@technova.com',
            'password' => 'readerpassword',
            'role' => 'reader'
        ]);
Profile::factory()->create(['user_id' => $reader->id]); // Intermediate




        $writer = User::factory()->create([
            'first_name' => 'TechNova Writer',
            'last_name' => 'User',
            'email' => 'writer@technova.com',
            'password' => 'writerpassword',
            'role' => 'writer'
        ]);
Profile::factory()->create(['user_id' => $writer->id, 'activity_score' => 65.00]); // Intermediate

        $tags = Tag::factory()->count(20)->create();


    User::factory()->count(10)->create()->each(function ($user) {

    // أ) إنشاء الـ Profile للمستخدم
    $profile = Profile::factory()->create([
        'user_id' => $user->id
    ]);

    // ب) إضافة الـ Avatar الخاص بالـ Profile عبر علاقة الـ Polymorphic الموحدة
    $profile->attachment()->save(Attachment::factory()->make([
        'file_type' => 'image/jpeg',
        'file_name' => 'avatar_' . $user->id . '.jpeg',
        'file_size' => fake()->numberBetween(50000, 2000000), // بين 50KB و 2MB
        'file_path' => $profile->avatar // نربطه بنفس المسار المولد بالـ factory كأفضلية لتناسق البيانات
    ]));
});

        // 4. إنشاء 30 مقالاً وربطها بالـ Tags والتعليقات والمرفقات
        Article::factory()->count(30)->create([
            'writer_id' => $writer->id // نربطهم بالكاتب الثابت للتجربة، أو اتركها عشوائية
        ])->each(function ($article) use ($tags) {
            // نختار من 2 إلى 5 وسوم عشوائية لكل مقال
            $randomTags = $tags->random(rand(2, 5))->pluck('id');
            $article->tags()->attach($randomTags);

            // ب) إضافة غلاف للمقال (MorphOne)
            $article->attachments()->save(Attachment::factory()->make([
                'file_type' => 'image/png'
            ]));

            // ج) إضافة تعليقات عشوائية للمقال (MorphMany)
            // كل مقال سيحصل على 1 إلى 5 تعليقات من مستخدمين عشوائيين
            Comment::factory()->count(rand(1, 5))->create([
                'commentable_id' => $article->id,
                'commentable_type' => Article::class,
                'body'=>fake()->paragraph(),
                'user_id' => User::inRandomOrder()->first()->id, // كاتب التعليق عشوائي
            ]);




    });
}
}
