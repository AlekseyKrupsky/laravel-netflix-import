<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Filters\MovieFilter;
use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Movie;

class MovieController extends Controller
{
    public function index(MovieFilter $filter): MovieCollection
    {
        return new MovieCollection(Movie::filter($filter)->paginate());
    }

    public function details(int $id): MovieResource
    {
        return new MovieResource(Movie::with('reviews', 'users')->findOrFail($id));
    }
}
