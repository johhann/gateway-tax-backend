<?php

namespace App\Enums;

enum ScheduleSession: string
{
    use HasEnumValues;

    case TALK_TO_A_TAX_EXPERT = 'talk_to_a_tax_expert';
    case PLAN_YOUR_REFUND = 'plan_your_refund';
    case SOLVE_TAX_QUESTIONS = 'solve_tax_questions';
    case EXPERT_SESSION_FOR_BUSINESS = 'expert_session_for_business';
}
