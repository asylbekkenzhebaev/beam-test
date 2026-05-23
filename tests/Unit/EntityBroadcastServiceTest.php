<?php

use App\Services\EntityBroadcastService;

test('entity broadcast service builds a consistent payload', function () {
    $payload = app(EntityBroadcastService::class)->buildPayload(
        entity: 'user',
        action: 'created',
        id: 42,
        title: 'User created',
        message: 'User "Alice" was created.',
        url: '/users/42/edit',
    );

    expect($payload)->toMatchArray([
        'entity' => 'user',
        'action' => 'created',
        'id' => 42,
        'title' => 'User created',
        'message' => 'User "Alice" was created.',
        'url' => '/users/42/edit',
    ]);

    expect($payload['timestamp'])->toBeString()->not->toBeEmpty();
});

test('entity broadcast service quietly skips when pusher config is incomplete', function () {
    config()->set('services.pusher', []);

    $result = app(EntityBroadcastService::class)->broadcast(
        entity: 'user',
        action: 'created',
        id: 1,
        title: 'User created',
        message: 'User "Alice" was created.',
        url: '/users/1/edit',
    );

    expect($result)->toBeNull();
});

test('entity broadcast service uses configured timeout for pusher client', function () {
    config()->set('services.pusher', [
        'app_id' => 'app-id',
        'key' => 'app-key',
        'secret' => 'app-secret',
        'cluster' => 'ap2',
        'use_tls' => true,
        'timeout' => 1,
    ]);

    $service = app(EntityBroadcastService::class);
    $method = new ReflectionMethod($service, 'makeClient');
    $method->setAccessible(true);

    $client = $method->invoke($service);

    expect($client->getSettings()['timeout'])->toBe(1.0);
});
