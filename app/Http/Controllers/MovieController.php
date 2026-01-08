<?php

namespace App\Http\Controllers;

use App\Dto\Global\FilterTypeRequest;
use App\Dto\Movie\StoreDto;
use App\Helpers\Movie\MovieHelper;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $movieHelper;
    public function __construct()
    {
        $this->movieHelper = new MovieHelper();
    }

    public function storeFilmTest(Request $request)
    {
        $data = $this->movieHelper->fetchMovie();

        return response()->json($data->message, $data->code);
    }

    public function store(Request $request)
    {
        $response = $this->movieHelper->store(StoreDto::fromRequest($request->all()));

        if (!$response->status) {
            return response()->json([
                'error' => $response->message,
                'message' => $response->message,
                'status_code' => $response->code,
                'dev' => $response->dev
            ], $response->code);
        }

        return response()->json([
            'message' => $response->message,
            'data' => $response->data,
            'status_code' => $response->code
        ], $response->code);
    }

    public function update(Request $request, $id)
    {
        $response = $this->movieHelper->updateMovie(StoreDto::fromRequest($request->all()));

        if (!$response->status) {
            return response()->json([
                'error' => $response->message,
                'message' => $response->message,
                'status_code' => $response->code,
                'dev' => $response->dev
            ], $response->code);
        }

        return response()->json([
            'message' => $response->message,
            'data' => $response->data,
            'status_code' => $response->code
        ], $response->code);
    }

    public function delete($id)
    {
        $response = $this->movieHelper->deleteMovie($id);

        if (!$response->status) {
            return response()->json([
                'error' => $response->message,
                'message' => $response->message,
                'status_code' => $response->code,
                'dev' => $response->dev
            ], $response->code);
        }

        return response()->json([
            'message' => $response->message,
            'data' => $response->data,
            'status_code' => $response->code
        ], $response->code);
    }

    public function getAllMovies(Request $request)
    {
        $response = $this->movieHelper->getAllMovies(FilterTypeRequest::fromRequest($request->all()));

        if (!$response->status) {
            return response()->json([
                'error' => $response->message,
                'message' => $response->message,
                'status_code' => $response->code,
                'dev' => $response->dev
            ], $response->code);
        }

        return response()->json([
            'message' => $response->message,
            'data' => $response->data,
            'status_code' => $response->code
        ], $response->code);
    }

    public function analyticsByReleaseDate(Request $request)
    {
        $response = $this->movieHelper->getAnalyticsByReleaseDate(FilterTypeRequest::fromRequest($request->all()));

        if (!$response->status) {
            return response()->json([
                'error' => $response->message,
                'message' => $response->message,
                'status_code' => $response->code,
                'dev' => $response->dev
            ], $response->code);
        }

        return response()->json([
            'message' => $response->message,
            'data' => $response->data,
            'status_code' => $response->code
        ], $response->code);
    }

    public function analyticsByAdult()
    {
        $response = $this->movieHelper->getAnalyticsByAdult();

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
    public function analyticsByRating()
    {
        $response = $this->movieHelper->getAnalyticsByRating();

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
