<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\User;

class UserController extends Controller
{
    public function index(UserFilter $filter): UserCollection
    {
        return new UserCollection(User::filter($filter)->paginate());
    }

    public function details(int $id): UserResource
    {
        return new UserResource(User::with('reviews', 'movies')->findOrFail($id));
    }
}
