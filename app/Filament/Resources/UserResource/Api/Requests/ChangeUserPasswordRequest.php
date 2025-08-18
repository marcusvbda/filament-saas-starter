<?php

namespace App\Filament\Resources\UserResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeUserPasswordRequest extends FormRequest
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
            "email" => ["required", "email", "exists:users,email"],
            "password" => ["required", "string", "confirmed"],
            "password_confirmation" => ["required", "string"],
            "token" => ["required", "string"],
        ];
    }
}
