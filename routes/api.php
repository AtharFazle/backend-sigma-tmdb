<?php

use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', [MovieController::class, 'storeFilmTest'])->middleware(EnsureTokenIsValid::class);

Route::prefix('v1')->group(function () {
    // Route::prefix('movies')->group(function () {

    // })

    Route::get('test', [MovieController::class, 'storeFilmTest'])->middleware(EnsureTokenIsValid::class);

    Route::prefix('analytics')->group(function () {
        Route::get('/genres', [GenreController::class, 'analyticsByGenres'])->middleware(EnsureTokenIsValid::class);
        Route::get('/release-date', [MovieController::class, 'analyticsByReleaseDate'])->middleware(EnsureTokenIsValid::class);
        Route::get('/adult', [MovieController::class, 'analyticsByAdult'])->middleware(EnsureTokenIsValid::class);
        Route::get('/rating', [MovieController::class, 'analyticsByRating'])->middleware(EnsureTokenIsValid::class);
    });

    Route::prefix('movies')->group(function () {
        Route::get('/', [MovieController::class, 'getAllMovies'])->middleware(EnsureTokenIsValid::class);
        Route::post('/', [MovieController::class, 'store'])->middleware(EnsureTokenIsValid::class);
        Route::put('/{id}', [MovieController::class, 'update'])->middleware(EnsureTokenIsValid::class);

        Route::delete('/{id}', [MovieController::class, 'delete'])->middleware(EnsureTokenIsValid::class);
    });

    Route::prefix('genres')->group(function () {
        Route::get('/', [GenreController::class, 'getGenreWhereHaveMovie'])->middleware(EnsureTokenIsValid::class);
        Route::get('/all', [GenreController::class, 'getGenreAll'])->middleware(EnsureTokenIsValid::class);
    });
});
