<?php

declare(strict_types=1);

namespace App;

use Filterable\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use Filterable;

    public $timestamps = false;

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reviews');
    }
}
