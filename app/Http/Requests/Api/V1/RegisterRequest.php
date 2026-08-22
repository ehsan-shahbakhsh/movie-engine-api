<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "RegisterRequest",
    required: ["name", "email", "password", "password_confirmation"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "Ehsan"),
        new OA\Property(property: "email", type: "string", format: "email", example: "ehsan@example.com"),
        new OA\Property(property: "password", type: "string", format: "password", example: "secret123"),
        new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "secret123"),
    ]
)]
class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ];
    }
}
