<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\OpenApi\Controllers\ProductControllerDoc;
use App\Services\EntityBroadcastService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends ProductControllerDoc
{
    public function index()
    {
        $products = Product::query()
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(10);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $product = Product::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        $product->tags()->sync($data['tags'] ?? []);
        $product->load(['category', 'tags']);

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'product',
            action: 'created',
            id: $product->id,
            title: 'Товар создан',
            message: sprintf('Товар "%s" был создан.', $product->name),
            url: route('products.index'),
        );

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Product $product): ProductResource
    {
        $product->load(['category', 'tags']);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $data = $request->validated();

        $product->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        $product->tags()->sync($data['tags'] ?? []);
        $product->load(['category', 'tags']);

        app(EntityBroadcastService::class)->broadcastAfterResponse(
            entity: 'product',
            action: 'updated',
            id: $product->id,
            title: 'Товар обновлен',
            message: sprintf('Товар "%s" был обновлен.', $product->name),
            url: route('products.index'),
        );

        return new ProductResource($product);
    }

    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }
}
