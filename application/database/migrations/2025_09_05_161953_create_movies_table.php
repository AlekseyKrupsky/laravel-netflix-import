<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('content_type', 100); // enum
            $table->string('genre_primary', 100)->nullable(); // enum ??
            $table->string('genre_secondary', 100)->nullable(); // enum ??
            $table->unsignedSmallInteger('release_year');
            $table->decimal('duration_minutes', 4, 1); // int ?++
            $table->string('rating', 10); // enum or short string
            $table->string('language', 15);
            $table->string('country_of_origin', 20);
            $table->decimal('imdb_rating', 3, 1)->nullable();
            $table->decimal('production_budget', 15, 1)->nullable(); // int ?
            $table->decimal('box_office_revenue', 15, 1)->nullable(); // int ?
            $table->unsignedSmallInteger('number_of_seasons')->nullable();
            $table->unsignedSmallInteger('number_of_episodes')->nullable();
            $table->boolean('is_netflix_original');
            $table->date('added_to_platform');
            $table->boolean('content_warning');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
