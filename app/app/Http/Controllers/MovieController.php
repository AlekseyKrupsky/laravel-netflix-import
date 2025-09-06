<?php

namespace App\Http\Controllers;

use App\Filters\MovieFilter;
use App\Http\Resources\MovieCollection;
use App\Movie;

class MovieController extends Controller
{
    public function index(MovieFilter $filter): MovieCollection
    {
        return new MovieCollection(Movie::filter($filter)->paginate());
    }
}
