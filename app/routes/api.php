<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Resources\MovieResource;
use App\Http\Resources\UserResource;
use App\Movie;
use App\User;
use Illuminate\Support\Facades\Route;

// move to controllers
Route::get('/movies/{id}', function (string $id) {
    return new MovieResource(Movie::with('reviews', 'users')->findOrFail($id));
});

Route::get('/movies', [MovieController::class, 'index']);

Route::get('/users/{id}', function (string $id) {
    return new UserResource(User::with('reviews', 'movies')->findOrFail($id));
});

Route::get('/users', [UserController::class, 'index']);

Route::group(['prefix' => '/reviews'], function () {
    Route::get('/', [ReviewController::class, 'index']);
    Route::get('/{id}', [ReviewController::class, 'details']);
    Route::post('/', [ReviewController::class, 'store']);
    Route::patch('/{review}', [ReviewController::class, 'update']);
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
});
