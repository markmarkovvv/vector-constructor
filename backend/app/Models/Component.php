<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Component extends Model
{
    protected $fillable = [
        'illustration_id',
        'title',
        'category',
        'svg_content',
        'view_box',
        'width',
        'height',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'width' => 'float',
        'height' => 'float',
    ];

    public function illustration(): BelongsTo
    {
        return $this->belongsTo(Illustration::class);
    }
}
