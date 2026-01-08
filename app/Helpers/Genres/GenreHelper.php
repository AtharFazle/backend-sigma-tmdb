<?php

namespace App\Helpers\Genres;

use App\Constants\StatusCodeConstant;
use App\Dto\Global\HelperResponseDto;
use App\Models\Genres;
use App\Models\Movies;
use App\Models\TMovieGenres;
use App\Traits\GlobalTrait;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class GenreHelper
 *
 * @package	App\Helpers
 * 
 */
class GenreHelper
{
    use GlobalTrait;

    public function __construct() {}


    public function getAllGenres()
    {
        try {
            $query = Genres::all();

            $data = $query->map(function ($genre) {
                return [
                    'label' => $genre->name,
                    'value' => $genre->id
                ];
            });

            return new HelperResponseDto(
                status: true,
                code: StatusCodeConstant::SUCCESS,
                message: 'Genres fetched successfully',
                data: $data
            );
        } catch (Throwable $e) {
            return new HelperResponseDto(
                status: false,
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                message: $e->getMessage(),
                dev: $this->devResponse($e)
            );
        }
    }

    public function getGenreWhereHaveMovie()
    {
        try {
            $data = TMovieGenres::query()
                ->with('genre')
                ->select('genre_id', DB::raw('MAX(id) as id'))
                ->groupBy('genre_id')
                ->get()->map(fn($genre) => $genre->genre);


            return new HelperResponseDto(
                status: true,
                code: StatusCodeConstant::SUCCESS,
                message: 'Genres fetched successfully',
                data: $data
            );
        } catch (Throwable $e) {

            return new HelperResponseDto(
                status: false,
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                message: $e->getMessage(),
                dev: $this->devResponse($e)
            );
        }
    }

    public function getAnalyticsByGenres()
    {
        try {
            $query = Genres::query()
                ->withCount('movies')
                ->whereHas('movies')

                ->get();

            $labels = [];
            $total = 0;
            $data = $query->map(function ($genre) use (&$labels, &$total) {
                $labels[] = $genre->name;
                $total += $genre->movies->count();

                return $genre->movies->count();
            });

            $topMember = $query->sortByDesc('movies_count')->take(3)->map(fn($genre) => $genre['name'])->values();


            $data = [
                'labels' => $labels,
                'series' => $data->toArray(),
                'total' => $total,
                'top_member' => $topMember
            ];

            return new HelperResponseDto(
                status: true,
                code: StatusCodeConstant::SUCCESS,
                message: 'Genres fetched successfully',
                data: $data
            );
        } catch (Throwable $e) {

            return new HelperResponseDto(
                status: false,
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                message: $e->getMessage(),
                dev: $this->devResponse($e)
            );
        }
    }
}
