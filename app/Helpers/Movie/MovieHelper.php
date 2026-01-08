<?php

namespace App\Helpers\Movie;

use App\Constants\StatusCodeConstant;
use App\Dto\Global\FilterTypeRequest;
use App\Dto\Global\HelperResponseDto;
use App\Dto\Movie\StoreDto;
use App\Http\Resources\MovieResources;
use App\Models\Movies;
use App\Models\Tmdb;
use App\Models\TMovieGenres;
use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

use function Illuminate\Log\log;

/**
 * Class MovieHelper
 *
 * @package	App\Helpers
 * 
 */
class MovieHelper
{
    use GlobalTrait;

    protected $url, $api_key, $token;

    public function __construct()
    {
        $this->url = "https://api.themoviedb.org/3/discover/movie?&page=1&sort_by=popularity.desc";
        $this->api_key = config(('app.api_key_tmdb'));
    }

    public function updateMovie(StoreDto $movie)
    {

        try {
            DB::beginTransaction();
            $movie_data = $movie->toArray();

            $movieQuery = Movies::query()
                ->find($movie->id);


            if (empty($movie)) {
                throw new Exception('Movie Not Found');
            }

            $movie_data['is_updated_by_user'] = true;

            $movieQuery->update($movie_data);
            $this->deleteAllTGenres($movieQuery->id, $movie->genres);
            $this->createTGenres($movieQuery->id, $movie->genres);

            DB::commit();

            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Movies fetched successfully',
                data: $movie
            );
        } catch (Throwable $e) {
            DB::rollBack();
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: "Something went wrong",
                dev: $this->devResponse($e),
            );
        }
    }

    public function getAllMovies(FilterTypeRequest $request)
    {
        try {
            $query = Movies::query()
                ->with('genres')
                ->orderBy('updated_at', 'desc')
                ->orderBy('is_updated_by_user', 'asc')
                ->orderBy('release_date', 'desc')
                ->when(!empty($request->search), function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%');
                })
                ->when(!empty($request->genres), function ($query) use ($request) {
                    $query->whereHas('genres', function ($query) use ($request) {
                        $query->whereIn('genre_id', $request->genres);
                    });
                })
                ->when(!empty($request->adult), function ($query) use ($request) {
                    $query->where('adult', $request->adult);
                })
                ->when(!empty($request->start_date) && !empty($request->end_date), function ($query) use ($request) {
                    $query->whereBetween('release_date', [$request->start_date, $request->end_date]);
                })
                ->paginate($request->per_page);

            $meta = [
                'search' => $request->search,
                'per_page' => $request->per_page,
                'page' => $request->page,
                'last_page' => $query->lastPage(),
                'total_data' => $query->total(),
            ];

            $data = [
                'meta' => $meta,
                'data' => MovieResources::collection($query->items()),
            ];

            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Movies fetched successfully',
                data: $data
            );
        } catch (Throwable $e) {
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: "Something went wrong",
                dev: $this->devResponse($e),
            );
        }
    }

    public function getAnalyticsByAdult()
    {
        try {
            $query = Movies::query()
                ->select(DB::raw('adult'), DB::raw('COUNT(*) as total_movies'))
                ->groupBy(DB::raw('adult'))
                ->get();


            $labels = $query->pluck('adult')->map(function ($item) {
                if ($item == 0) {
                    return 'Not Adult';
                } else {
                    return 'Adult';
                }
            });

            $series = $query->pluck('total_movies');

            $data = [
                'labels' => $labels,
                'series' => $series,
                'total' => $query->sum('total_movies'),
            ];

            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Analytics By Adult fetched successfully',
                data: $data
            );
        } catch (Throwable $e) {
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: "Something went wrong",
                dev: $this->devResponse($e),
            );
        }
    }
    public function getAnalyticsByRating()
    {
        try {
            $query = Movies::query()
                ->select(
                    DB::raw("CASE WHEN vote_average >= 6 THEN 'Excellent' ELSE 'Worst' END as rating_category"),
                    DB::raw('COUNT(*) as total_movies')
                )
                ->groupBy(DB::raw("CASE WHEN vote_average >= 6 THEN 'Excellent' ELSE 'Worst' END"))
                ->get();

            $labels = $query->pluck('rating_category');

            $series = $query->pluck('total_movies');

            $data = [
                'labels' => $labels,
                'series' => $series,
                'total' => $query->sum('total_movies'),
            ];

            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Analytics By Rating fetched successfully',
                data: $data
            );
        } catch (Throwable $e) {
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: "Something went wrong",
                dev: $this->devResponse($e),
            );
        }
    }


    public function store(StoreDto $request)
    {
        try {
            $movie_data = $request->toArray();
            $movie = Movies::create($movie_data);
            $this->createTGenres($movie->id, $request->genres);
            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Movie stored successfully',
                // data: Movies::create($request->toArray()),
            );
        } catch (Throwable $e) {
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: "Something went wrong",
                dev: $this->devResponse($e),
            );
        }
    }


    public function deleteMovie($id)
    {
        try {
            DB::beginTransaction();
            $movie = Movies::find($id);

            if (empty($movie)) {
                throw new Exception('Movie Not Found');
            }

            TMovieGenres::where('movie_id', $movie->id)->delete();
            // $this->deleteAllTGenres($movie->id, $movie->genres);
            $movie->delete();

            DB::commit();
            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Movie deleted successfully',
                data: [
                    'id' => $movie->id
                ],
            );
        } catch (Throwable $e) {
            DB::rollBack();
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: "Something went wrong",
                dev: $this->devResponse($e),
            );
        }
    }


    public function fetchMovie()
    {
        try {
            $client = new Client();

            $tmdb = Tmdb::find(1);

            if (empty($tmdb)) {
                $tmdb = Tmdb::create([
                    'id' => 1,
                    'page' => 1,
                    'total_page' => 1,
                    'repeat' => 0,
                ]);
            }

            $response = $client->request('GET', $this->url . '&api_key=' . $this->api_key . '&page=' . $tmdb->page);

            if ($response->getStatusCode() >= 400) {
                throw new Exception($response->getBody());
            }

            $data = json_decode($response->getBody(), true);


            $database = $this->insertFilm($data['results']);

            if (!$database->status) {
                throw new Exception($database->message);
            }

            $tmdb->last_page = $data['total_pages'];
            if ($tmdb->last_page == $tmdb->page) {
                $tmdb->page = 1;
                $tmdb->repeat += 1;
            } else {
                $tmdb->page = $tmdb->page + 1;
            }

            $tmdb->save();


            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Film fetched successfully',
                data: $data,
                dev: $this->devResponseSuccess('Success fetch movie from TMDB API'),
            );
        } catch (Throwable $e) {
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: 'Error: ' . $e->getMessage(),
                dev: $this->devResponse($e),
            );
        }
    }

    public function getAnalyticsByReleaseDate(FilterTypeRequest $request)
    {
        try {
            $query = Movies::query()
                ->select(DB::raw('YEAR(release_date) as year'), DB::raw('MONTH(release_date) as month'), DB::raw('COUNT(*) as total_movies'))
                ->whereBetween('release_date', [$request->start_date, $request->end_date])
                ->groupBy(DB::raw('YEAR(release_date)'), DB::raw('MONTH(release_date)'))
                ->orderBy(DB::raw('YEAR(release_date)'), 'asc')
                ->orderBy(DB::raw('MONTH(release_date)'), 'asc')
                ->get();

            $labels = [];
            $series = [];

            $query->each(function ($movie) use (&$labels, &$series) {
                $labels[] = Carbon::createFromDate($movie->year, $movie->month)->format('M Y');
                $series[] = $movie->total_movies;
            });

            $data = [
                'labels' => $labels,
                'series' => $series
            ];

            return new HelperResponseDto(
                status: true,
                code: StatusCodeConstant::SUCCESS,
                message: 'Movies grouped by month fetched successfully',
                data: $data
            );
        } catch (Throwable $e) {
            return new HelperResponseDto(
                status: false,
                dev: $this->devResponse($e),
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                message: 'Something went wrong',
            );
        }
    }


    private function deleteAllTGenres(string $movie_id, array $genre_id)
    {
        TMovieGenres::where('movie_id', $movie_id)->delete();
    }

    private function createTGenres(string $movie_id, array $genre_id)
    {
        foreach ($genre_id as $genre) {
            TMovieGenres::create([
                'movie_id' => $movie_id,
                'genre_id' => $genre
            ]);
        };
    }

    private function insertFilm($data)
    {
        try {
            DB::beginTransaction();

            $data = collect($data)->each(function ($value, $key) {
                $movie = Movies::find($value['id']);

                if (!empty($movie)) {
                    if ($movie->is_updated_by_user) {
                    } else {
                        $movie->update($value);
                    }
                    return;
                }

                $formattedData = $this->formatData($value);

                $t_movie_genres = collect($value['genre_ids'])->map(function ($genre_id) use ($formattedData) {
                    return ['genre_id' => $genre_id, 'movie_id' => $formattedData['id']];
                });

                $t_movie_genres->each(function ($t_movie_genre) {
                    TMovieGenres::firstOrCreate(
                        ['movie_id' =>  $t_movie_genre['movie_id'], 'genre_id' => $t_movie_genre['genre_id']],
                        $t_movie_genre
                    );
                });

                $movie = Movies::create($formattedData);
                return $formattedData;
            });


            DB::commit();
            return new HelperResponseDto(
                code: StatusCodeConstant::SUCCESS,
                status: true,
                message: 'Film fetched successfully',
                data: $data,
                dev: $this->devResponseSuccess('Success fetch movie from TMDB API', $data),
            );
        } catch (Throwable $e) {
            DB::rollBack();
            return new HelperResponseDto(
                code: StatusCodeConstant::INTERNAL_SERVER_ERROR,
                status: false,
                message: $e->getMessage(),
                dev: $this->devResponse($e),
            );
        }
    }

    private function formatData($data)
    {
        if (!is_array($data)) {
            throw new \Exception('Data must be an array');
        }

        return [
            'id' => isset($data['id']) ? (string) $data['id'] : null,
            'title' => isset($data['original_title']) ? $data['original_title'] : null,

            'languages' => isset($data['original_language']) ? $data['original_language'] : null,

            'overview' => isset($data['overview']) ? $data['overview'] : null,
            'adult' => isset($data['adult']) ? (bool) $data['adult'] : false,

            'backdrop_path' => isset($data['backdrop_path']) ? $data['backdrop_path'] : null,
            'poster_path' => isset($data['poster_path']) ? $data['poster_path'] : null,

            'popularity' => isset($data['popularity']) ? (float) $data['popularity'] : 0,
            // 'genres' => isset($data['genre_ids']) && is_array($data['genre_ids']) ? implode(',', $data['genre_ids']) : '',

            'vote_average' => isset($data['vote_average']) ? (float) $data['vote_average'] : 0,
            'vote_count' => isset($data['vote_count']) ? (int) $data['vote_count'] : 0,

            'release_date' => isset($data['release_date']) ? Carbon::parse($data['release_date'])->toDateString() : null,
        ];
    }
}
