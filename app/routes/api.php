<?php

use App\Movie;
use App\Review;
use App\User;
use Illuminate\Support\Facades\Route;

Route::get('/movies/{id}', function (string $id) {
    return new \App\Http\Resources\MovieResource(Movie::findOrFail($id));
});

Route::get('/movies', function () {
    return new \App\Http\Resources\MovieResource(Movie::paginate());
});

Route::get('/users/{id}', function (string $id) {
    return new \App\Http\Resources\UserResource(User::findOrFail($id));
});

Route::get('/users', function () {
    return new \App\Http\Resources\UserResource(User::paginate());
});

Route::get('/reviews/{id}', function (string $id) {
    return new \App\Http\Resources\ReviewResource(Review::findOrFail($id));
});

Route::get('/reviews', function () {
    return new \App\Http\Resources\ReviewResource(Review::paginate());
});
