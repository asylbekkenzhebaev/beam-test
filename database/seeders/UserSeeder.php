<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->users() as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                ],
            );
        }
    }

    private function users(): array
    {
        return [
            [
                'name' => 'Асанов Асан',
                'email' => 'asan.asanov@example.com',
            ],
            [
                'name' => 'Анна Смирнова',
                'email' => 'anna.smirnova@example.com',
            ],
            [
                'name' => 'Даниил Кузнецов',
                'email' => 'daniil.kuznetsov@example.com',
            ],
        ];
    }
}
