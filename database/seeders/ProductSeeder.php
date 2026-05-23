<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->get()->keyBy('name');
        $tags = Tag::query()->get()->keyBy('name');

        foreach ($this->products() as $productData) {
            $product = Product::query()->updateOrCreate(
                ['name' => $productData['name']],
                [
                    'category_id' => $categories[$productData['category']]->id,
                    'price' => $productData['price'],
                ],
            );

            $product->tags()->sync(
                collect($productData['tags'])
                    ->map(fn (string $name): int => $tags[$name]->id)
                    ->all(),
            );
        }
    }

    private function products(): array
    {
        return [
            [
                'category' => 'Игровые ноутбуки',
                'name' => 'ASUS ROG Strix G16',
                'price' => 1899.99,
                'tags' => ['RTX', '32GB RAM', 'SSD', 'Для игр'],
            ],
            [
                'category' => 'Игровые ноутбуки',
                'name' => 'Lenovo Legion Pro 7',
                'price' => 2149.00,
                'tags' => ['RTX', 'IPS', 'Wi-Fi 6', 'Для игр'],
            ],
            [
                'category' => 'Ультрабуки',
                'name' => 'Dell XPS 14',
                'price' => 1299.50,
                'tags' => ['OLED', 'USB-C', 'Легкий', 'Автономный'],
            ],
            [
                'category' => 'Ультрабуки',
                'name' => 'MacBook Air 13',
                'price' => 1399.00,
                'tags' => ['Премиум', 'Легкий'],
            ],
            [
                'category' => 'Ноутбуки для работы',
                'name' => 'HP ProBook 450',
                'price' => 1199.90,
                'tags' => ['16GB RAM', 'SSD', 'Для офиса', 'USB-C'],
            ],
            [
                'category' => 'Ноутбуки для работы',
                'name' => 'Lenovo ThinkPad E16',
                'price' => 1499.00,
                'tags' => ['32GB RAM', 'Для офиса', 'Премиум'],
            ],
            [
                'category' => 'Ноутбуки для учебы',
                'name' => 'Acer Aspire 5',
                'price' => 799.99,
                'tags' => ['16GB RAM', 'SSD', 'Для учебы', 'Автономный'],
            ],
            [
                'category' => 'Ноутбуки для учебы',
                'name' => 'Xiaomi RedmiBook 15',
                'price' => 899.00,
                'tags' => ['IPS', 'Wi-Fi 6', 'Для учебы'],
            ],
            [
                'category' => 'Аксессуары для ноутбуков',
                'name' => 'Anker USB-C Hub 8-in-1',
                'price' => 159.99,
                'tags' => ['USB-C', 'Для офиса'],
            ],
            [
                'category' => 'Аксессуары для ноутбуков',
                'name' => 'Rain Design mStand',
                'price' => 89.00,
                'tags' => ['Легкий', 'Премиум', 'Для офиса'],
            ],
        ];
    }
}
