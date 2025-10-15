<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property ?string $email
 * @property ?string $phone
 * @property string $password
 * @property string|null $role
 * @property bool|null $is_mobile
 * @property bool|null $remember_me
 *
 * @method bool filled(string $key)
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required_if:phone,null|email',
            'password' => 'required|string',
            'remember_me' => 'nullable|boolean',
        ];
    }
}
