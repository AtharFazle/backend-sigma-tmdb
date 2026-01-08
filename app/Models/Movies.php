<?php

namespace App\Models;

use App\Constants\FillableConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movies extends Model
{

    use HasFactory;

    protected $table = "m_movie";
    protected $guarded = ['languages'];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genres::class, "t_movie_genres", "movie_id", "genre_id");
    }

    public function index()
    {
        return $this->orderBy("updated_at", "desc")->orderBy("name", "asc")->get();
    }
}
