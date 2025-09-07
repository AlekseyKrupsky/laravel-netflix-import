<?php

declare(strict_types=1);

namespace App\Enum\User;

enum Gender: string
{
    case Male = 'Male';
    case Female = 'Female';
    case Other = 'Other';
    case PreferNotToSay = 'Prefer not to say';
}
