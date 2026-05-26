<?php

namespace App\Models;

use App\Enums\ArticleCategory;
use App\Enums\ArticleStatus;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{ use SoftDeletes,HasFactory;
    protected $fillable = [
        'title',
        'content',
        'writer_id',
        'status',
        'published_at',
        'slug',
        'category',
    ];

    protected $casts=[
        'published_at'=>'datetime',
        'category'=> ArticleCategory::class,
        'status'=> ArticleStatus::class,
    ];

    protected $appends = ['reading_time'];




// accessor
public function readingTime():Attribute
{
return Attribute::make(
    get: function() {
    $wordCount = str_word_count($this->content);
    $averageReadingSpeed = 200; // words per minute
    $readingTimeMinutes = ceil($wordCount / $averageReadingSpeed);
    return $readingTimeMinutes . ' min read';
});
}

//mutator
public function title():Attribute
{
    return Attribute::make(
        set: fn($value) => ucfirst($value)
    );
    }



    public function writer():BelongsTo
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function comments():MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments():MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }



     public function tags():MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
