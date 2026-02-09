<?php

namespace App;

enum SubscriptionStatus: string
{
    case Unpaid = 'unpaid';
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
}
