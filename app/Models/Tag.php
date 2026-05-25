<?php

namespace App\Models;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{ use HasFactory;
    protected $fillable = [
        'name','slug',
    ];
protected $casts=[
    'created_at'=>'datetime',
];
    public function articles():MorphToMany
    {
        return $this->morphedByMany(Article::class, 'taggable');
    }
}
