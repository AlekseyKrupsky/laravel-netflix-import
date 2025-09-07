<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\DeviceType;
use App\Enum\Sentiment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'exists:users,id',
            'movie_id' => 'exists:movies,id',
            'rating' => 'integer|min:0|max:5',
            'review_date' => 'date',
            'device_type' => [Rule::enum(DeviceType::class)],
            'is_verified_watch' => 'boolean',
            'helpful_votes' => 'nullable|integer|min:0|max:65535',
            'total_votes' => 'nullable|integer|min:0|max:65535',
            'review_text' => 'nullable|string|max:255',
            'sentiment' => [Rule::enum(Sentiment::class)],
            'sentiment_score' => ['nullable', Rule::numeric()->min(0.000)->max(1.000)->decimal(0, 3)],
        ];
    }
}
