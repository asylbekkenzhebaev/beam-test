<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->tags() as $name) {
            Tag::query()->updateOrCreate(
                ['name' => $name],
                ['name' => $name],
            );
        }
    }

    private function tags(): array
    {
        return [
            'RTX',
            'OLED',
            'IPS',
            '16GB RAM',
            '32GB RAM',
            'SSD',
            'USB-C',
            'Thunderbolt',
            'Wi-Fi 6',
            'Легкий',
            'Премиум',
            'Для офиса',
            'Для учебы',
            'Для игр',
            'Автономный',
        ];
    }
}
