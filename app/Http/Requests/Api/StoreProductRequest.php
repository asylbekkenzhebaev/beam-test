<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreProductRequest',
    required: ['category_id', 'name', 'price'],
    properties: [
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Demo Product'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 149.99),
        new OA\Property(
            property: 'tags',
            type: 'array',
            items: new OA\Items(type: 'integer'),
            example: [1, 2]
        ),
    ],
    type: 'object',
)]
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'tags' => ['array'],
            'tags.*' => ['integer', 'distinct', 'exists:tags,id'],
        ];
    }
}
