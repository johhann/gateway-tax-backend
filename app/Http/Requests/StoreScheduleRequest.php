<?php

namespace App\Http\Requests;

use App\Enums\MeetingType;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'scheduled_start_time' => ['required', 'date', 'before:scheduled_end_time'],
            'scheduled_end_time' => ['required', 'date', 'after:scheduled_start_time'],
            'type' => ['required', 'string', Rule::in(MeetingType::values())],
            'branch_id' => ['required_if:type,=,in_person_meeting', 'exists:branches,id'],
        ];
    }
}
