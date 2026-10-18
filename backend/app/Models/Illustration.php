<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Illustration extends Model
{
    protected $fillable = [
        'title',
        'category',
        'svg_content',
        'view_box',
        'description',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }
}
