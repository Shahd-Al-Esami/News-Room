<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Comment extends Model
{ use SoftDeletes, HasFactory;
    protected $fillable = [
        'user_id',
        'body',
    ];
protected $casts=[
    'created_at'=>'datetime',
];

static public function booted(){

    static::created(function($comment){
        Cache::tags(['Comments'])->flush();
     });
     static::deleted(function($comment){
        Cache::tags(['Comments'])->flush();
     });
     static::updated(function ($comment) {
        Cache::tags(['Comments'])->flush();
        
    });
    static::forceDeleted(function ($comment) {
        Cache::tags(['Comments'])->flush();
        });

        static::restored(function ($comment) {
        Cache::tags(['Comments'])->flush();
        });


}

    public function commentable():MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
