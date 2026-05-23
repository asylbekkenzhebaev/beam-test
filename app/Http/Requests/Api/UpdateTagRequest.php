<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateTagRequest',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'sale'),
    ],
    type: 'object',
)]
class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tag = $this->route('tag');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags', 'name')->ignore($tag),
            ],
        ];
    }
}
