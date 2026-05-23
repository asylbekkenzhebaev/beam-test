<?php

namespace App\Livewire\Categories;

use App\Models\Category;
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

    #[On('category-saved')]
    public function handleSaved(string $status): void
    {
        $this->statusMessage = $status;
        $this->errorMessage = null;
        $this->closeModal();
    }

    #[On('category-form-cancelled')]
    public function handleCancelled(): void
    {
        $this->closeModal();
    }

    public function delete(int $id)
    {
        $category = Category::query()->withCount('products')->findOrFail($id);

        if ($category->products_count > 0) {
            $this->errorMessage = 'Сначала удалите товары из этой категории или перенесите их в другую.';
            $this->statusMessage = null;

            return;
        }

        $category->delete();

        $this->statusMessage = 'Категория удалена.';
        $this->errorMessage = null;
    }

    public function refreshFromBroadcast(): void
    {
        $this->lastExternalRefreshAt = now()->getTimestampMs();
    }

    public function render()
    {
        return view('livewire.categories.index', [
            'categories' => Category::query()
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
