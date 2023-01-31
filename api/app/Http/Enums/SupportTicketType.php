<?php

namespace App\Http\Enums;

abstract class SupportTicketType
{
    const UNSPECIFIED = 0;
    const TECHNICAL = 10;
    const BANNED = 20;
    const MISSING_PRICE = 30;
}
