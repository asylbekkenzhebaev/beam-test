<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreUserRequest',
    required: ['name', 'email'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Alice'),
        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'alice@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, maxLength: 255, example: '+996 555 123 456'),
    ],
    type: 'object',
)]
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}
