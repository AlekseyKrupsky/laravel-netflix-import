<?php

namespace App\Http\Controllers;

use App\Filters\ReviewFilter;
use App\Http\Resources\ReviewResource;
use App\Review;

class ReviewController extends Controller
{
    public function index(ReviewFilter $filter): ReviewResource
    {
        return new ReviewResource(Review::filter($filter)->paginate());
    }
}
