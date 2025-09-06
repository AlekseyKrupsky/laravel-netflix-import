<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Filters\ReviewFilter;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewCollection;
use App\Http\Resources\ReviewResource;
use App\Review;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    public function index(ReviewFilter $filter): ReviewCollection
    {
        return new ReviewCollection(Review::filter($filter)->paginate());
    }

    public function details(int $id): ReviewResource
    {
        return new ReviewResource(Review::with('movie', 'user')->findOrFail($id));
    }

    public function store(StoreReviewRequest $request): ReviewResource
    {
        $review = Review::create($request->validated());

        return new ReviewResource($review);
    }

    public function update(Review $review, UpdateReviewRequest $request): ReviewResource
    {
        $review->update($request->validated());

        return new ReviewResource($review);
    }

    public function destroy(int $id): Response
    {
        Review::destroy($id);

        return response(status: 204);
    }
}
