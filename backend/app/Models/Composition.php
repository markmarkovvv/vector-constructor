<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Composition extends Model
{
    protected $fillable = [
        'title',
        'canvas_width',
        'canvas_height',
        'canvas_data',
        'svg_preview',
    ];

    protected $casts = [
        'canvas_data' => 'array',
        'canvas_width' => 'integer',
        'canvas_height' => 'integer',
    ];
}
