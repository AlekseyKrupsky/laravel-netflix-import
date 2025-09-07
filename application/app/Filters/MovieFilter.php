<?php

declare(strict_types=1);

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MovieFilter extends Filter
{
    /**
     * @var array<int, string>
     */
    protected array $filters = ['id', 'title', 'genre', 'genre_secondary', 'year'];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    protected function id(string $value): Builder
    {
        $ids = explode(',', $value);

        return $this->getBuilder()->whereIn('id', $ids);
    }

    protected function title(string $value): Builder
    {
        return $this->getBuilder()->where('title', $value);
    }

    protected function genre(string $value): Builder
    {
        return $this->getBuilder()->where('genre_primary', $value);
    }

    protected function genreSecondary(string $value): Builder
    {
        return $this->getBuilder()->where('genre_secondary', $value);
    }

    protected function year(string $value): Builder
    {
        return $this->getBuilder()->where('release_year', $value);
    }
}
