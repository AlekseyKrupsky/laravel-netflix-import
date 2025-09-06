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

//    /**
//     * Show the form for creating a new resource.
//     */
//    public function create()
//    {
//        //
//    }

//    /**
//     * Store a newly created resource in storage.
//     */
//    public function store(StoreMovieRequest $request)
//    {
//        //
//    }

//    /**
//     * Display the specified resource.
//     */
//    public function show(Movie $movie)
//    {
//        //
//    }

//    /**
//     * Show the form for editing the specified resource.
//     */
//    public function edit(Movie $movie)
//    {
//        //
//    }

//    /**
//     * Update the specified resource in storage.
//     */
//    public function update(UpdateMovieRequest $request, Movie $movie)
//    {
//        //
//    }

//    /**
//     * Remove the specified resource from storage.
//     */
//    public function destroy(Movie $movie)
//    {
//        //
//    }
}
