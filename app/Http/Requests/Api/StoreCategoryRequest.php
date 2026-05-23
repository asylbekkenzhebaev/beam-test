<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreCategoryRequest',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Hardware'),
    ],
    type: 'object',
)]
class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ];
    }
}
