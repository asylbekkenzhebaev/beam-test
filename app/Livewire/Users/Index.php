<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?string $statusMessage = null;

    public ?string $errorMessage = null;

    public function create(): void
    {
        $this->resetMessages();
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->resetMessages();
        $this->editingId = $id;
        $this->showFormModal = true;
    }

    public function closeModal(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
    }

    #[On('user-saved')]
    public function handleSaved(string $status): void
    {
        $this->statusMessage = $status;
        $this->errorMessage = null;
        $this->closeModal();
    }

    #[On('user-form-cancelled')]
    public function handleCancelled(): void
    {
        $this->closeModal();
    }

    public function delete(int $id)
    {
        User::query()->findOrFail($id)->delete();

        $this->statusMessage = 'Пользователь удален.';
        $this->errorMessage = null;
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => User::query()
                ->latest()
                ->get(),
        ]);
    }

    private function resetMessages(): void
    {
        $this->statusMessage = null;
        $this->errorMessage = null;
    }
}
