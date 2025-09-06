<?php

declare(strict_types=1);

namespace App\Enum;

enum Sentiment: string
{
    case Positive = 'positive';
    case Neutral = 'neutral';
    case Negative = 'negative';
}
