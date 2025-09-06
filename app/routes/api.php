<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Resources\MovieResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\UserResource;
use App\Movie;
use App\Review;
use App\User;
use Illuminate\Support\Facades\Route;

Route::get('/movies/{id}', function (string $id) {
    return new MovieResource(Movie::findOrFail($id));
});

Route::get('/movies', [MovieController::class, 'index']);

Route::get('/users/{id}', function (string $id) {
    return new UserResource(User::findOrFail($id));
});

Route::get('/users', [UserController::class, 'index']);

Route::get('/reviews/{id}', function (string $id) {
    return new ReviewResource(Review::findOrFail($id));
});

Route::get('/reviews', [ReviewController::class, 'index']);
