<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TagResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'featured'),
        new OA\Property(property: 'products_count', type: 'integer', nullable: true, example: 2),
        new OA\Property(
            property: 'products',
            type: 'array',
            nullable: true,
            items: new OA\Items(ref: '#/components/schemas/ProductSummary')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
class TagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'products_count' => $this->whenCounted('products'),
            'products' => $this->whenLoaded('products', fn () => $this->products->map(fn ($product): array => [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'name' => $product->name,
                'price' => $product->price,
                'created_at' => $product->created_at?->toISOString(),
                'updated_at' => $product->updated_at?->toISOString(),
            ])->values()->all()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
