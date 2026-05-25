<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Profile extends Model
{ use HasFactory;
    protected $fillable = [
        'user_id',
        'bio',
        'phone',
        'avatar','activity_score'
    ];
protected $casts=[
    'created_at'=>'datetime',
    'activity_score'=>'decimal:2',
];

protected $appends = ['level'];
//accessor
public function level():Attribute
{
return Attribute::make(
    get: function() {
    if ($this->activity_score >= 80) {
        return 'Expert';
    } elseif ($this->activity_score >= 50) {
        return 'Intermediate';
    } else {
        return 'Beginner';
    }
});}


//scope
public function scopeTop($query)
{
    return $query->orderBy('activity_score', 'desc');
}

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,);
    }

     public function attachment():MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
