<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\OpenApi\Controllers\UserControllerDoc;
use App\Services\EntityBroadcastService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends UserControllerDoc
{
    public function index()
    {
        $users = User::query()
            ->latest()
            ->paginate(10);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'user',
            action: 'created',
            id: $user->id,
            title: 'Пользователь создан',
            message: sprintf('Пользователь "%s" был создан.', $user->name),
            url: route('users.index'),
        );

        return (new UserResource($user))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'user',
            action: 'updated',
            id: $user->id,
            title: 'Пользователь обновлен',
            message: sprintf('Пользователь "%s" был обновлен.', $user->name),
            url: route('users.index'),
        );

        return new UserResource($user);
    }

    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
