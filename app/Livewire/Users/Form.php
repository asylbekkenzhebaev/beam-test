<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Services\EntityBroadcastService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?int $recordId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public function mount(?int $recordId = null): void
    {
        $this->recordId = $recordId;

        if ($recordId === null) {
            return;
        }

        $user = User::query()->with('profile')->findOrFail($recordId);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->profile?->phone ?? '';
    }

    public function save(EntityBroadcastService $broadcasts)
    {
        $data = $this->validate($this->rules());
        $phone = trim((string) ($data['phone'] ?? ''));
        unset($data['phone']);

        if ($this->recordId === null) {
            $user = User::query()->create($data);
            $this->syncProfile($user, $phone);

            $broadcasts->broadcastAfterResponse(
                entity: 'user',
                action: 'created',
                id: $user->id,
                title: 'Пользователь создан',
                message: sprintf('Пользователь "%s" был создан.', $user->name),
                url: route('users.index'),
            );

            $this->dispatch('user-saved', status: 'Пользователь создан.');

            return;
        }

        $user = User::query()->findOrFail($this->recordId);
        $user->update($data);
        $this->syncProfile($user, $phone);

        $broadcasts->broadcastAfterResponse(
            entity: 'user',
            action: 'updated',
            id: $user->id,
            title: 'Пользователь обновлен',
            message: sprintf('Пользователь "%s" был обновлен.', $user->name),
            url: route('users.index'),
        );

        $this->dispatch('user-saved', status: 'Пользователь обновлен.');
    }

    public function cancel(): void
    {
        $this->dispatch('user-form-cancelled');
    }

    public function render()
    {
        return view('livewire.users.form');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->recordId),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function syncProfile(User $user, string $phone): void
    {
        if ($phone === '') {
            $user->profile()->delete();

            return;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['phone' => $phone],
        );
    }
}
