<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use App\Services\EntityBroadcastService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?int $recordId = null;

    public string $name = '';

    public function mount(?int $recordId = null): void
    {
        $this->recordId = $recordId;

        if ($recordId === null) {
            return;
        }

        $category = Category::query()->findOrFail($recordId);

        $this->name = $category->name;
    }

    public function save(EntityBroadcastService $broadcasts)
    {
        $data = $this->validate($this->rules());

        if ($this->recordId === null) {
            $category = Category::query()->create($data);

            $broadcasts->broadcastAfterResponse(
                entity: 'category',
                action: 'created',
                id: $category->id,
                title: 'Категория создана',
                message: sprintf('Категория "%s" была создана.', $category->name),
                url: route('categories.index'),
            );

            $this->dispatch('category-saved', status: 'Категория создана.');

            return;
        }

        $category = Category::query()->findOrFail($this->recordId);
        $category->update($data);

        $broadcasts->broadcastAfterResponse(
            entity: 'category',
            action: 'updated',
            id: $category->id,
            title: 'Категория обновлена',
            message: sprintf('Категория "%s" была обновлена.', $category->name),
            url: route('categories.index'),
        );

        $this->dispatch('category-saved', status: 'Категория обновлена.');
    }

    public function cancel(): void
    {
        $this->dispatch('category-form-cancelled');
    }

    public function render()
    {
        return view('livewire.categories.form');
    }

    private function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->recordId),
            ],
        ];
    }
}
