<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Http\Resources\UserResource;
use App\User;

class UserController extends Controller
{
    public function index(UserFilter $filter): UserResource
    {
        return new UserResource(User::filter($filter)->paginate());
    }
}
