<?php

namespace App\Http\Controllers;

use App\Filters\ReviewFilter;
use App\Http\Resources\ReviewCollection;
use App\Review;

class ReviewController extends Controller
{
    public function index(ReviewFilter $filter): ReviewCollection
    {
        return new ReviewCollection(Review::filter($filter)->paginate());
    }
}
