<?php

declare(strict_types=1);

namespace App\Enum\Review;

enum DeviceType: string
{
    case Mobile = 'Mobile';
    case SmartTV = 'Smart TV';
    case Tablet = 'Tablet';
    case Laptop = 'Laptop';
}
