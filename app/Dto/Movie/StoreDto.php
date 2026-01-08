<?php

namespace App\Dto\Movie;

use App\Traits\GlobalTrait;
use DateTime;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Class StoreDto
 *
 * @package	App\Dto
 * 
 */
class StoreDto
{

    public function __construct(
        public ?string $id,
        public string $title,
        public int $vote_average,
        // public int $vote_count,
        public DateTime $release_date,
        public int $adult,
        public string $languages,
        // public UploadedFile $img,
        public string $overview,
        public array $genres,

    ) {}

    public static function fromRequest(array $request): StoreDto
    {
        return new self(
            id: $request['id'] ?? null,
            title: $request['title'],
            vote_average: $request['rating'],
            languages: $request['languages'] ?? 'id',
            release_date: new DateTime($request['release_date']),
            adult: $request['adult'],
            overview: $request['overview'],
            genres: $request['genres'],
        );
    }
    public static function fromRequestStore(array $request): StoreDto
    {
        return new self(
            id: $request['id'] ?? null,
            title: $request['title'],
            vote_average: $request['rating'],
            languages: $request['languages'] ?? 'id',
            release_date: new DateTime($request['release_date']),
            adult: $request['adult'],
            overview: $request['overview'],
            genres: $request['genres'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id ?? null,
            'title' => $this->title,
            'vote_average' => $this->vote_average,
            'languages' => $this->languages,
            'release_date' => $this->release_date->format('Y-m-d'), // Format the date as needed
            'adult' => $this->adult,
            'overview' => $this->overview,
            'genres' => $this->genres,
            'is_updated_by_user' => true,
        ];
    }
}
