<?php

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReviewFilter extends Filter
{
    /**
     * @var array<int, string>
     */
    protected array $filters = ['id', 'user', 'movie', 'review_date', 'device_type', 'is_verified_watch'];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    protected function id(string $value): Builder
    {
        $ids = explode(',', $value);

        return $this->getBuilder()->whereIn('id', $ids);
    }

    protected function user(string $value): Builder
    {
        return $this->getBuilder()->where('user_id', $value);
    }

    protected function movie(string $value): Builder
    {
        return $this->getBuilder()->where('movie_id', $value);
    }

    protected function reviewDate(string $value): Builder
    {
        return $this->getBuilder()->where('review_date', $value);
    }

    protected function deviceType(string $value): Builder
    {
        return $this->getBuilder()->where('device_type', $value);
    }

    protected function isVerifiedWatch(string $value): Builder
    {
        return $this->getBuilder()->where('is_verified_watch', $value);
    }
}
