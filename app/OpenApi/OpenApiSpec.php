<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Catalog REST API',
    description: 'Public REST API for users, categories, products, and tags.',
)]
#[OA\Server(url: '/', description: 'Application root')]
#[OA\Tag(name: 'Users')]
#[OA\Tag(name: 'Categories')]
#[OA\Tag(name: 'Products')]
#[OA\Tag(name: 'Tags')]
#[OA\Schema(
    schema: 'UserProfileResource',
    properties: [
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+996 555 123 456'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UserSummary',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Alice'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'alice@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+996 555 123 456'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'CategorySummary',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Hardware'),
        new OA\Property(property: 'products_count', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ProductSummary',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Demo Product'),
        new OA\Property(property: 'price', type: 'string', example: '149.99'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TagSummary',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'featured'),
        new OA\Property(property: 'products_count', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PaginationLink',
    properties: [
        new OA\Property(property: 'url', type: 'string', nullable: true, example: 'http://localhost/api/users?page=2'),
        new OA\Property(property: 'label', type: 'string', example: 'Next &raquo;'),
        new OA\Property(property: 'active', type: 'boolean', example: false),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PaginationLinks',
    properties: [
        new OA\Property(property: 'first', type: 'string', nullable: true, example: 'http://localhost/api/users?page=1'),
        new OA\Property(property: 'last', type: 'string', nullable: true, example: 'http://localhost/api/users?page=1'),
        new OA\Property(property: 'prev', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'next', type: 'string', nullable: true, example: null),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PaginationMeta',
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 1),
        new OA\Property(
            property: 'links',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/PaginationLink')
        ),
        new OA\Property(property: 'path', type: 'string', example: 'http://localhost/api/users'),
        new OA\Property(property: 'per_page', type: 'integer', example: 10),
        new OA\Property(property: 'to', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'total', type: 'integer', example: 1),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                    example: ['The email field is required.']
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UserPagination',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResource')),
        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'CategoryPagination',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/CategoryResource')),
        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ProductPagination',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductResource')),
        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TagPagination',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/TagResource')),
        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ConflictError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Delete products or move them before removing this category.'),
    ],
    type: 'object',
)]
class OpenApiSpec {}
