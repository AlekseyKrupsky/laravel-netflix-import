<?php

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilter extends Filter
{
    /**
     * @var array<int, string>
     */
    protected array $filters = ['id', 'email', 'first_name', 'last_name', 'age', 'gender'];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    protected function id(string $value): Builder
    {
        $ids = explode(',', $value);

        return $this->getBuilder()->whereIn('id', $ids);
    }

    protected function email(string $value): Builder
    {
        return $this->getBuilder()->where('email', $value);
    }

    protected function firstName(string $value): Builder
    {
        return $this->getBuilder()->where('first_name', $value);
    }

    protected function lastName(string $value): Builder
    {
        return $this->getBuilder()->where('last_name', $value);
    }

    protected function age(string $value): Builder
    {
        return $this->getBuilder()->where('age', $value);
    }

    protected function gender(string $value): Builder
    {
        return $this->getBuilder()->where('gender', $value);
    }
}
