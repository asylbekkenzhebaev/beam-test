<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreTagRequest;
use App\Http\Requests\Api\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\OpenApi\Controllers\TagControllerDoc;
use App\Services\EntityBroadcastService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TagController extends TagControllerDoc
{
    public function index()
    {
        $tags = Tag::query()
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return TagResource::collection($tags);
    }

    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());
        $tag->loadCount('products');

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'tag',
            action: 'created',
            id: $tag->id,
            title: 'Тег создан',
            message: sprintf('Тег "%s" был создан.', $tag->name),
            url: route('tags.index'),
        );

        return (new TagResource($tag))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Tag $tag): TagResource
    {
        $tag->loadCount('products')->load('products');

        return new TagResource($tag);
    }

    public function update(UpdateTagRequest $request, Tag $tag): TagResource
    {
        $tag->update($request->validated());
        $tag->loadCount('products');

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'tag',
            action: 'updated',
            id: $tag->id,
            title: 'Тег обновлен',
            message: sprintf('Тег "%s" был обновлен.', $tag->name),
            url: route('tags.index'),
        );

        return new TagResource($tag);
    }

    public function destroy(Tag $tag): Response
    {
        $tag->delete();

        return response()->noContent();
    }
}
