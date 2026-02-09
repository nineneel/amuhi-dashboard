<?php

namespace App;

enum EventStatus: string
{
    case Upcoming = 'upcoming';
    case Ongoing = 'ongoing';
    case Past = 'past';
    case Cancelled = 'cancelled';
}
