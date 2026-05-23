<?php

namespace App\OpenApi\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCategoryRequest;
use App\Http\Requests\Api\UpdateCategoryRequest;
use App\Models\Category;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Categories')]
abstract class CategoryControllerDoc extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        tags: ['Categories'],
        summary: 'List categories',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated categories',
                content: new OA\JsonContent(ref: '#/components/schemas/CategoryPagination')
            ),
        ]
    )]
    abstract public function index();

    #[OA\Post(
        path: '/api/categories',
        tags: ['Categories'],
        summary: 'Create a category',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreCategoryRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Category created', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function store(StoreCategoryRequest $request);

    #[OA\Get(
        path: '/api/categories/{category}',
        tags: ['Categories'],
        summary: 'Show a category',
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category details', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResource')),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    abstract public function show(Category $category);

    #[OA\Put(
        path: '/api/categories/{category}',
        tags: ['Categories'],
        summary: 'Update a category',
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateCategoryRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Category updated', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function update(UpdateCategoryRequest $request, Category $category);

    #[OA\Delete(
        path: '/api/categories/{category}',
        tags: ['Categories'],
        summary: 'Delete a category',
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Category deleted'),
            new OA\Response(response: 409, description: 'Category has products', content: new OA\JsonContent(ref: '#/components/schemas/ConflictError')),
        ]
    )]
    abstract public function destroy(Category $category);
}
