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
            ->with('profile')
            ->latest()
            ->paginate(10);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $payload = $request->validated();
        $phone = trim((string) ($payload['phone'] ?? ''));
        unset($payload['phone']);

        $user = User::create($payload);
        $this->syncProfile($user, $phone);

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'user',
            action: 'created',
            id: $user->id,
            title: 'Пользователь создан',
            message: sprintf('Пользователь "%s" был создан.', $user->name),
            url: route('users.index'),
        );

        return (new UserResource($user->load('profile')))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('profile'));
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $payload = $request->validated();
        $phone = trim((string) ($payload['phone'] ?? ''));
        unset($payload['phone']);

        $user->update($payload);
        $this->syncProfile($user, $phone);

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'user',
            action: 'updated',
            id: $user->id,
            title: 'Пользователь обновлен',
            message: sprintf('Пользователь "%s" был обновлен.', $user->name),
            url: route('users.index'),
        );

        return new UserResource($user->load('profile'));
    }

    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }

    private function syncProfile(User $user, string $phone): void
    {
        if ($phone === '') {
            $user->profile()->delete();

            return;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['phone' => $phone],
        );
    }
}
