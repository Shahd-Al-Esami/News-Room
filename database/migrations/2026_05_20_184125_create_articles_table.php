<?php

use App\Enums\ArticleCategory;
use App\Enums\ArticleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->foreignId('writer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', array_column(ArticleStatus::cases(),'value'))->default(ArticleStatus::Draft->value);
            $table->timestamp('published_at')->nullable();
            $table->string('slug')->unique();
            $table->enum('category', array_column(ArticleCategory::cases(),'value'))->default(ArticleCategory::Technology->value);
            $table->timestamps();
            $table->softDeletes();

            $table->index([ 'status', 'published_at' ]);//composite index
            $table->index([ 'writer_id', 'status' ]);//composite index
            $table->index('category');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
