<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\Review\DeviceType;
use App\Enum\Review\Sentiment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'movie_id' => 'required|exists:movies,id',
            'rating' => 'required|integer|min:0|max:5',
            'review_date' => 'required|date',
            'device_type' => ['required', Rule::enum(DeviceType::class)],
            'is_verified_watch' => 'required|boolean',
            'helpful_votes' => 'nullable|integer|min:0|max:65535',
            'total_votes' => 'nullable|integer|min:0|max:65535',
            'review_text' => 'nullable|string|max:255',
            'sentiment' => ['required', Rule::enum(Sentiment::class)],
            'sentiment_score' => ['nullable', Rule::numeric()->min(0.000)->max(1.000)->decimal(3)],
        ];
    }
}
