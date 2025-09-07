<?php

declare(strict_types=1);

namespace App\Enum\User;

enum SubscriptionPlan: string
{
    case Basic = 'Basic';
    case Standard = 'Standard';
    case Premium = 'Premium';
    case PremiumPlus = 'Premium+';
}
