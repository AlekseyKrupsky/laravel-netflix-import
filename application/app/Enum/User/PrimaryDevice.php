<?php

declare(strict_types=1);

namespace App\Enum\User;

enum PrimaryDevice: string
{
    case Desktop = 'Desktop';
    case Tablet = 'Tablet';
    case Laptop = 'Laptop';
    case GamingConsole = 'Gaming Console';
    case Mobile = 'Mobile';
    case SmartTV = 'Smart TV';
}
