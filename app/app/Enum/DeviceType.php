<?php

declare(strict_types=1);

namespace App\Enum;

enum DeviceType: string
{
    case Mobile = 'Mobile';
    case SmartTV = 'Smart TV';
    case Tablet = 'Tablet';
    case Laptop = 'Laptop';
}
