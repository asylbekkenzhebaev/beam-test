<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->users() as $user) {
            $record = User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                ],
            );

            $record->profile()->updateOrCreate(
                ['user_id' => $record->id],
                ['phone' => $user['phone']],
            );
        }
    }

    private function users(): array
    {
        return [
            [
                'name' => 'Асанов Асан',
                'email' => 'asan.asanov@example.com',
                'phone' => '+996 555 111 222',
            ],
            [
                'name' => 'Анна Смирнова',
                'email' => 'anna.smirnova@example.com',
                'phone' => '+996 700 222 333',
            ],
            [
                'name' => 'Даниил Кузнецов',
                'email' => 'daniil.kuznetsov@example.com',
                'phone' => '+996 772 333 444',
            ],
        ];
    }
}
