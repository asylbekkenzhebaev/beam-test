<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?int $lastExternalRefreshAt = null;

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

    #[On('product-saved')]
    public function handleSaved(string $status): void
    {
        $this->statusMessage = $status;
        $this->errorMessage = null;
        $this->closeModal();
    }

    #[On('product-form-cancelled')]
    public function handleCancelled(): void
    {
        $this->closeModal();
    }

    public function delete(int $id)
    {
        Product::query()->findOrFail($id)->delete();

        $this->statusMessage = 'Товар удален.';
        $this->errorMessage = null;
    }

    public function refreshFromBroadcast(): void
    {
        $this->lastExternalRefreshAt = now()->getTimestampMs();
    }

    public function render()
    {
        return view('livewire.products.index', [
            'products' => Product::query()
                ->with(['category', 'tags'])
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
