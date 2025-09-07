<?php

use App\Enum\DeviceType;
use App\Enum\Sentiment;
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
            $table->enum('device_type', [DeviceType::Mobile, DeviceType::SmartTV, DeviceType::Tablet, DeviceType::Laptop]);
            $table->boolean('is_verified_watch');
            $table->unsignedSmallInteger('helpful_votes')->nullable();
            $table->unsignedSmallInteger('total_votes')->nullable();
            $table->string('review_text')->nullable();
            $table->enum('sentiment', [Sentiment::Positive, Sentiment::Neutral, Sentiment::Negative]);
            $table->decimal('sentiment_score', 4, 3)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
