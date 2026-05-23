<?php

namespace App\OpenApi\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Models\User;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Users')]
abstract class UserControllerDoc extends Controller
{
    #[OA\Get(
        path: '/api/users',
        tags: ['Users'],
        summary: 'List users',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated users',
                content: new OA\JsonContent(ref: '#/components/schemas/UserPagination')
            ),
        ]
    )]
    abstract public function index();

    #[OA\Post(
        path: '/api/users',
        tags: ['Users'],
        summary: 'Create a user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreUserRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function store(StoreUserRequest $request);

    #[OA\Get(
        path: '/api/users/{user}',
        tags: ['Users'],
        summary: 'Show a user',
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User details', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    abstract public function show(User $user);

    #[OA\Put(
        path: '/api/users/{user}',
        tags: ['Users'],
        summary: 'Update a user',
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateUserRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'User updated', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    abstract public function update(UpdateUserRequest $request, User $user);

    #[OA\Delete(
        path: '/api/users/{user}',
        tags: ['Users'],
        summary: 'Delete a user',
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'User deleted'),
        ]
    )]
    abstract public function destroy(User $user);
}
