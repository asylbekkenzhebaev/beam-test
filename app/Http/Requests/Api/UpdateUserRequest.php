<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateUserRequest',
    required: ['name', 'email'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Alice Updated'),
        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'alice.updated@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, maxLength: 255, example: '+996 555 123 456'),
    ],
    type: 'object',
)]
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}
