<?php

use App\Enum\User\Gender;
use App\Enum\User\PrimaryDevice;
use App\Enum\User\SubscriptionPlan;
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
            $table->enum('gender', [Gender::Male, Gender::Female, Gender::Other, Gender::PreferNotToSay])->nullable();
            $table->string('country', 20);
            $table->string('state_province', 25);
            $table->string('city', 25);
            $table->enum('subscription_plan', [SubscriptionPlan::Basic, SubscriptionPlan::Standard, SubscriptionPlan::Premium, SubscriptionPlan::PremiumPlus]);
            $table->date('subscription_start_date');
            $table->boolean('is_active');
            $table->decimal('monthly_spend', 6)->nullable();
            $table->enum('primary_device', [PrimaryDevice::Desktop, PrimaryDevice::Tablet, PrimaryDevice::Laptop, PrimaryDevice::GamingConsole, PrimaryDevice::Mobile, PrimaryDevice::SmartTV]);
            $table->unsignedTinyInteger('household_size')->nullable();
            $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
