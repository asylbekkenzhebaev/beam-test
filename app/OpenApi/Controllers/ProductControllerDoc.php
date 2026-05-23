<?php

namespace App\OpenApi\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Models\Product;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Products')]
abstract class ProductControllerDoc extends Controller
{
    #[OA\Get(
        path: '/api/products',
        tags: ['Products'],
        summary: 'List products',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated products',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductPagination')
            ),
        ]
    )]
    abstract public function index();

    #[OA\Post(
        path: '/api/products',
        tags: ['Products'],
        summary: 'Create a product',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreProductRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created', content: new OA\JsonContent(ref: '#/components/schemas/ProductResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function store(StoreProductRequest $request);

    #[OA\Get(
        path: '/api/products/{product}',
        tags: ['Products'],
        summary: 'Show a product',
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details', content: new OA\JsonContent(ref: '#/components/schemas/ProductResource')),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    abstract public function show(Product $product);

    #[OA\Put(
        path: '/api/products/{product}',
        tags: ['Products'],
        summary: 'Update a product',
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProductRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product updated', content: new OA\JsonContent(ref: '#/components/schemas/ProductResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function update(UpdateProductRequest $request, Product $product);

    #[OA\Delete(
        path: '/api/products/{product}',
        tags: ['Products'],
        summary: 'Delete a product',
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Product deleted'),
        ]
    )]
    abstract public function destroy(Product $product);
}
