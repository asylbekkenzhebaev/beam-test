<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Demo Product'),
        new OA\Property(property: 'price', type: 'string', example: '149.99'),
        new OA\Property(property: 'category', ref: '#/components/schemas/CategorySummary'),
        new OA\Property(
            property: 'tags',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/TagSummary')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'price' => $this->price,
            'category' => $this->whenLoaded('category', fn (): ?array => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'products_count' => $this->category->products_count ?? null,
                'created_at' => $this->category->created_at?->toISOString(),
                'updated_at' => $this->category->updated_at?->toISOString(),
            ] : null),
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
                'products_count' => $tag->products_count ?? null,
                'created_at' => $tag->created_at?->toISOString(),
                'updated_at' => $tag->updated_at?->toISOString(),
            ])->values()->all()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
