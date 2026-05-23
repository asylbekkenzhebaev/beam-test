<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreCategoryRequest;
use App\Http\Requests\Api\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\OpenApi\Controllers\CategoryControllerDoc;
use App\Services\EntityBroadcastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CategoryController extends CategoryControllerDoc
{
    public function index()
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());
        $category->loadCount('products');

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'category',
            action: 'created',
            id: $category->id,
            title: 'Категория создана',
            message: sprintf('Категория "%s" была создана.', $category->name),
            url: route('categories.index'),
        );

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Category $category): CategoryResource
    {
        $category->loadCount('products')->load('products');

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());
        $category->loadCount('products');

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'category',
            action: 'updated',
            id: $category->id,
            title: 'Категория обновлена',
            message: sprintf('Категория "%s" была обновлена.', $category->name),
            url: route('categories.index'),
        );

        return new CategoryResource($category);
    }

    public function destroy(Category $category): Response|JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Delete products or move them before removing this category.',
            ], ResponseAlias::HTTP_CONFLICT);
        }

        $category->delete();

        return response()->noContent();
    }
}
