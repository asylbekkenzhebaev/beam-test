<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Alice'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'alice@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+996 555 123 456'),
        new OA\Property(
            property: 'profile',
            ref: '#/components/schemas/UserProfileResource',
            nullable: true,
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->profile?->phone,
            'profile' => $this->profile === null ? null : [
                'phone' => $this->profile->phone,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
