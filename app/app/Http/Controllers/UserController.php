<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Http\Resources\UserCollection;
use App\User;

class UserController extends Controller
{
    public function index(UserFilter $filter): UserCollection
    {
        return new UserCollection(User::filter($filter)->paginate());
    }
}
