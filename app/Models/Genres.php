<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genres extends Model
{

    protected $table = 'm_genres';
    protected $guarded = [''];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movies::class, "t_movie_genres", "genre_id", "movie_id");
    }
}
