<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->unique();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other', 'Prefer not to say'])->nullable(); // TODO:: Add enum class
            $table->string('country', 20);
            $table->string('state_province', 25);
            $table->string('city', 25);
            $table->enum('subscription_plan', ['Basic', 'Standard', 'Premium', 'Premium+']);  // TODO:: Add enum class
            $table->date('subscription_start_date');
            $table->boolean('is_active');
            $table->decimal('monthly_spend', 6)->nullable();
            $table->enum('primary_device', ['Desktop', 'Tablet', 'Laptop', 'Gaming Console', 'Mobile', 'Smart TV']); // TODO:: Add enum class
            $table->unsignedTinyInteger('household_size')->nullable();
            $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
