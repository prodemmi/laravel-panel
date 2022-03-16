<?php

namespace Prodemmi\Lava;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    protected $fillable = [
        'disk',
        'path',
        'filename',
        'extension',
        'mime_type',
        'size'
    ];

}
