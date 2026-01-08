<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'adult' => $this->adult,
            'genres' => $this->genres,
            'release_date' => Carbon::parse($this->release_date),
            'languages' => $this->languages,

            'overview' => $this->overview,
            'vote_average' => $this->vote_average,

            'img' => 'https://image.tmdb.org/t/p/w500' . $this->backdrop_path
        ];
    }
}
