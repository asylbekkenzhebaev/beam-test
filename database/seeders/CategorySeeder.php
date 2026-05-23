<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->categories() as $name) {
            Category::query()->updateOrCreate(
                ['name' => $name],
                ['name' => $name],
            );
        }
    }

    private function categories(): array
    {
        return [
            'Игровые ноутбуки',
            'Ультрабуки',
            'Ноутбуки для работы',
            'Ноутбуки для учебы',
            'Аксессуары для ноутбуков',
        ];
    }
}
