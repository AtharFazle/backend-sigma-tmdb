<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TMovieGenres extends Model
{
    use HasFactory, HasUuids;

    protected $table = "t_movie_genres";

    protected $guarded = [''];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movies::class, 'movie_id', 'id');
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genres::class, 'genre_id', 'id');
    }
}
