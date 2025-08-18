<?php

namespace App\Filament\Resources\UserResource\Api\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "name" => ["nullable", "string", "max:255"],
            "profession" => ["nullable", "string", "max:255"],
            "civil_status" => ["nullable", "string", "max:255", "in:" . implode(",", User::$civilStatuses)],
            "nacionality" => ["nullable", "string", "max:255"],
            "cpf_or_cnpj" => ["nullable", "string", "cpf_ou_cnpj", "max:255"],
            "password" => ["nullable", "string", "confirmed", "max:255"],
            "password_confirmation" => ["nullable", "string", "max:255"],
            "phones" => ["nullable", "array"],
            "phones.*.type" => ["required_with:phones", "string", "max:255"],
            "phones.*.number" => ["required_with:phones", "string", "max:15"],
            "addresses" => ["nullable", "array"],
            "addresses.*.name" => ["required_with:addresses", "string", "max:255"],
            "addresses.*.zipcode" => ["required_with:addresses", "string", "max:255"],
            "addresses.*.street" => ["required_with:addresses", "string", "max:255"],
            "addresses.*.number" => ["required_with:addresses", "string", "max:20"],
            "addresses.*.complement" => ["nullable", "string", "max:255"],
            "addresses.*.district" => ["required_with:addresses", "string", "max:255"],
            "addresses.*.state" => ["required_with:addresses", "string", "max:2"],
            "addresses.*.city" => ["required_with:addresses", "string", "max:255"],

        ];
    }
}
