<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

test('users api index returns paginated users', function () {
    User::factory()->create([
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ]);

    $this->getJson('/api/users')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.name', 'Alice')
        ->assertJsonPath('data.0.email', 'alice@example.com');
});

test('users api show returns user details', function () {
    $user = User::factory()->create([
        'name' => 'Alice',
    ]);

    $this->getJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJsonPath('id', $user->id)
        ->assertJsonPath('name', 'Alice');
});

test('categories api returns counts and show includes products', function () {
    $category = Category::factory()->create([
        'name' => 'Hardware',
    ]);
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Demo Product',
    ]);

    $this->getJson('/api/categories')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Hardware')
        ->assertJsonPath('data.0.products_count', 1);

    $this->getJson("/api/categories/{$category->id}")
        ->assertOk()
        ->assertJsonPath('products.0.id', $product->id)
        ->assertJsonPath('products.0.name', 'Demo Product');
});

test('products api show includes category and tags', function () {
    $category = Category::factory()->create([
        'name' => 'Hardware',
    ]);
    $tags = Tag::factory()->count(2)->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Demo Product',
    ]);
    $product->tags()->sync($tags->pluck('id')->all());

    $this->getJson("/api/products/{$product->id}")
        ->assertOk()
        ->assertJsonPath('name', 'Demo Product')
        ->assertJsonPath('category.name', 'Hardware')
        ->assertJsonCount(2, 'tags');
});

test('tags api returns counts and show includes products', function () {
    $tag = Tag::factory()->create([
        'name' => 'featured',
    ]);
    $product = Product::factory()->create([
        'name' => 'Demo Product',
    ]);
    $product->tags()->sync([$tag->id]);

    $this->getJson('/api/tags')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'featured')
        ->assertJsonPath('data.0.products_count', 1);

    $this->getJson("/api/tags/{$tag->id}")
        ->assertOk()
        ->assertJsonPath('products.0.id', $product->id)
        ->assertJsonPath('products.0.name', 'Demo Product');
});

test('users api can create and update users', function () {
    $createResponse = $this->postJson('/api/users', [
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ]);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('name', 'Alice')
        ->assertJsonPath('email', 'alice@example.com');

    $userId = $createResponse->json('id');

    $this->putJson("/api/users/{$userId}", [
        'name' => 'Alice Updated',
        'email' => 'alice@example.com',
    ])->assertOk()
        ->assertJsonPath('name', 'Alice Updated');
});

test('categories api can create, update, and protect deletion with products', function () {
    $createResponse = $this->postJson('/api/categories', [
        'name' => 'Hardware',
    ]);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('name', 'Hardware');

    $categoryId = $createResponse->json('id');

    $this->putJson("/api/categories/{$categoryId}", [
        'name' => 'Devices',
    ])->assertOk()
        ->assertJsonPath('name', 'Devices');

    Product::factory()->create([
        'category_id' => $categoryId,
    ]);

    $this->deleteJson("/api/categories/{$categoryId}")
        ->assertStatus(409)
        ->assertJsonPath('message', 'Delete products or move them before removing this category.');
});

test('products api can create update and delete products with synced tags', function () {
    $category = Category::factory()->create();
    $otherCategory = Category::factory()->create();
    $tags = Tag::factory()->count(2)->create();

    $createResponse = $this->postJson('/api/products', [
        'category_id' => $category->id,
        'name' => 'Demo Product',
        'price' => 149.99,
        'tags' => $tags->pluck('id')->all(),
    ]);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('name', 'Demo Product')
        ->assertJsonCount(2, 'tags');

    $productId = $createResponse->json('id');

    $this->putJson("/api/products/{$productId}", [
        'category_id' => $otherCategory->id,
        'name' => 'Updated Product',
        'price' => 199.99,
        'tags' => [$tags->first()->id],
    ])->assertOk()
        ->assertJsonPath('name', 'Updated Product')
        ->assertJsonPath('category.id', $otherCategory->id)
        ->assertJsonCount(1, 'tags');

    $this->deleteJson("/api/products/{$productId}")
        ->assertNoContent();
});

test('tags api can create update and delete tags', function () {
    $createResponse = $this->postJson('/api/tags', [
        'name' => 'featured',
    ]);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('name', 'featured');

    $tagId = $createResponse->json('id');

    $this->putJson("/api/tags/{$tagId}", [
        'name' => 'sale',
    ])->assertOk()
        ->assertJsonPath('name', 'sale');

    $this->deleteJson("/api/tags/{$tagId}")
        ->assertNoContent();
});

test('invalid api payload returns validation errors', function () {
    $this->postJson('/api/users', [
        'name' => '',
        'email' => 'not-an-email',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

test('swagger documentation route loads and generated spec includes catalog endpoints', function () {
    Artisan::call('l5-swagger:generate');

    $this->get('/api/documentation')
        ->assertOk();

    $spec = file_get_contents(storage_path('api-docs/api-docs.json'));

    expect($spec)->toContain('/api/users');
    expect($spec)->not->toContain('/api/profiles');
    expect($spec)->toContain('/api/categories');
    expect($spec)->toContain('/api/products');
    expect($spec)->toContain('/api/tags');
    expect($spec)->toContain('UserResource');
    expect($spec)->toContain('ProductResource');
});
