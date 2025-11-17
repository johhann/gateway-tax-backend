<?php

namespace App\Enums;

enum ScheduleSession: string
{
    use HasEnumValues;

    case TALK_TO_A_TAX_EXPERT = 'Talk to a Tax Expert';
    case PLAN_YOUR_REFUND = 'Plan your Refund';
    case SOLVE_TAX_QUESTIONS = "Let's Solve Your Tax Questions";
    case EXPERT_SESSION_FOR_BUSINESS = 'Expert Session for Your Business';
}
