<?php

namespace App\OpenApi\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTagRequest;
use App\Http\Requests\Api\UpdateTagRequest;
use App\Models\Tag;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tags')]
abstract class TagControllerDoc extends Controller
{
    #[OA\Get(
        path: '/api/tags',
        tags: ['Tags'],
        summary: 'List tags',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated tags',
                content: new OA\JsonContent(ref: '#/components/schemas/TagPagination')
            ),
        ]
    )]
    abstract public function index();

    #[OA\Post(
        path: '/api/tags',
        tags: ['Tags'],
        summary: 'Create a tag',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreTagRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Tag created', content: new OA\JsonContent(ref: '#/components/schemas/TagResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function store(StoreTagRequest $request);

    #[OA\Get(
        path: '/api/tags/{tag}',
        tags: ['Tags'],
        summary: 'Show a tag',
        parameters: [
            new OA\Parameter(name: 'tag', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Tag details', content: new OA\JsonContent(ref: '#/components/schemas/TagResource')),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    abstract public function show(Tag $tag);

    #[OA\Put(
        path: '/api/tags/{tag}',
        tags: ['Tags'],
        summary: 'Update a tag',
        parameters: [
            new OA\Parameter(name: 'tag', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateTagRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Tag updated', content: new OA\JsonContent(ref: '#/components/schemas/TagResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function update(UpdateTagRequest $request, Tag $tag);

    #[OA\Delete(
        path: '/api/tags/{tag}',
        tags: ['Tags'],
        summary: 'Delete a tag',
        parameters: [
            new OA\Parameter(name: 'tag', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Tag deleted'),
        ]
    )]
    abstract public function destroy(Tag $tag);
}
