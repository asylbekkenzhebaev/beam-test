<?php

namespace App\Livewire\Tags;

use App\Models\Tag;
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

    #[On('tag-saved')]
    public function handleSaved(string $status): void
    {
        $this->statusMessage = $status;
        $this->errorMessage = null;
        $this->closeModal();
    }

    #[On('tag-form-cancelled')]
    public function handleCancelled(): void
    {
        $this->closeModal();
    }

    public function delete(int $id)
    {
        Tag::query()->findOrFail($id)->delete();

        $this->statusMessage = 'Тег удален.';
        $this->errorMessage = null;
    }

    public function render()
    {
        return view('livewire.tags.index', [
            'tags' => Tag::query()
                ->withCount('products')
                ->orderBy('name')
                ->get(),
        ]);
    }

    private function resetMessages(): void
    {
        $this->statusMessage = null;
        $this->errorMessage = null;
    }
}
