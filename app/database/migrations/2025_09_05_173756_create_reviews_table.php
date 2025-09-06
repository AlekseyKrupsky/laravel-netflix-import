<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('movie_id');
            $table->unsignedTinyInteger('rating');
            $table->date('review_date');
            $table->enum('device_type', ['Mobile', 'Smart TV', 'Tablet', 'Laptop']);
            $table->boolean('is_verified_watch');
            $table->unsignedTinyInteger('helpful_votes')->nullable();
            $table->unsignedTinyInteger('total_votes')->nullable();
            $table->string('review_text'); // max len?
            $table->enum('sentiment', ['positive', 'neutral', 'negative']);
            $table->decimal('sentiment_score', 4, 3)->nullable(); // float ??
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
