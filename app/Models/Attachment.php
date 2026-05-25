<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{ use SoftDeletes, HasFactory;
    protected $fillable = [

        'file_path',
        'file_size',
        'file_type',
        'file_name',
    ];
protected $casts=[
    'created_at'=>'datetime',
    'file_size'=>'integer',
];
    public function attachable():MorphTo
    {
        return $this->morphTo();
    }
}
