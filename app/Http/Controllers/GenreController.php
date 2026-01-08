<?php

namespace App\Http\Controllers;

use App\Dto\Global\FilterTypeRequest;
use App\Helpers\Genres\GenreHelper;
use Illuminate\Http\Request;

class GenreController extends Controller
{

    protected $genreHelper;
    public function __construct()
    {
        $this->genreHelper = new GenreHelper();
    }

    public function getAllGenres()
    {
        $helper = $this->genreHelper->getAllGenres();
        return $helper;
    }

    public function analyticsByGenres()
    {
        $response = $this->genreHelper->getAnalyticsByGenres();

        if (!$response->status) {
            return response()->json([
                'error' => $response->message,
                'message' => $response->message,
                'status_code' => $response->code,
            ], $response->code);
        }

        return response()->json([
            'message' => $response->message,
            'data' => $response->data,
            'status_code' => $response->code
        ], $response->code);
    }

    public function getGenreWhereHaveMovie()
    {
        $helper = $this->genreHelper->getGenreWhereHaveMovie();

        if (!$helper->status) {
            return response()->json($helper->message, $helper->code);
        }

        return response()->json($helper->data, $helper->code);
    }

    public function getGenreAll()
    {
        $response = $this->genreHelper->getAllGenres();

        if (!$response->status) {
            return response()->json([
                'error' => $response->message,
                'message' => $response->message,
                'status_code' => $response->code,
            ], $response->code);
        }

        return response()->json([
            'message' => $response->message,
            'data' => $response->data,
            'status_code' => $response->code
        ], $response->code);
    }
}
