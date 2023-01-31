<?php

namespace App\Http\Enums;

abstract class SupportTicketPriority
{
    const GUEST = 0;
    const USER = 10;
    const SPECIAL_USER = 20;
    const CO_CASTER = 30;
    const STREAMER = 40;
    const ADMIN = 50;
}
