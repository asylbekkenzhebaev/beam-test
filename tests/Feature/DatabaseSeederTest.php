<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Artisan;

test('entity seeders create the expected laptop dataset', function () {
    Artisan::call('db:seed', ['--class' => DatabaseSeeder::class]);

    expect(User::query()->count())->toBe(3);
    expect(Category::query()->count())->toBe(5);
    expect(Product::query()->count())->toBe(10);
    expect(Tag::query()->count())->toBe(15);

    Product::query()->with(['category', 'tags'])->get()->each(function (Product $product): void {
        expect($product->category)->not->toBeNull();
        expect($product->tags->count())->toBeGreaterThanOrEqual(2);
        expect($product->tags->count())->toBeLessThanOrEqual(4);
    });

    $gamingCategory = Category::query()->where('name', 'Игровые ноутбуки')->first();
    $ultrabookCategory = Category::query()->where('name', 'Ультрабуки')->first();
    $studyTag = Tag::query()->where('name', 'Для учебы')->first();

    expect($gamingCategory)->not->toBeNull();
    expect($ultrabookCategory)->not->toBeNull();
    expect($studyTag)->not->toBeNull();

    expect(
        Product::query()->where('name', 'ASUS ROG Strix G16')->first()?->tags()->count()
    )->toBe(4);

    expect(
        Product::query()
            ->whereHas('tags', fn ($query) => $query->where('name', 'Для учебы'))
            ->count()
    )->toBeGreaterThanOrEqual(2);
});
