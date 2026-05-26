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

    $profile = Profile::factory()->create([
        'user_id' => $user->id
    ]);

    $profile->attachment()->save(Attachment::factory()->make([
        'file_type' => 'image/jpeg',
        'file_name' => 'avatar_' . $user->id . '.jpeg',
        'file_size' => fake()->numberBetween(50000, 2000000),
        'file_path' => $profile->avatar
    ]));
});

        Article::factory()->count(30)->create([
            'writer_id' => $writer->id
        ])->each(function ($article) use ($tags) {
            $randomTags = $tags->random(rand(2, 5))->pluck('id');
            $article->tags()->attach($randomTags);

            $article->attachments()->save(Attachment::factory()->make([
                'file_type' => 'image/png'
            ]));

            Comment::factory()->count(rand(1, 5))->create([
                'commentable_id' => $article->id,
                'commentable_type' => Article::class,
                'body'=>fake()->paragraph(),
                'user_id' => User::inRandomOrder()->first()->id,
            ]);




    });
}
}
