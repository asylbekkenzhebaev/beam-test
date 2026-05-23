<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Services\EntityBroadcastService;
use Livewire\Component;

class Form extends Component
{
    public ?int $recordId = null;

    public ?string $category_id = null;

    public string $name = '';

    public ?string $price = null;

    public array $tags = [];

    public function mount(?int $recordId = null): void
    {
        $this->recordId = $recordId;

        if ($recordId === null) {
            return;
        }

        $product = Product::query()->with('tags')->findOrFail($recordId);

        $this->category_id = (string) $product->category_id;
        $this->name = $product->name;
        $this->price = (string) $product->price;
        $this->tags = $product->tags->pluck('id')->map(fn (int $id): string => (string) $id)->all();
    }

    public function save(EntityBroadcastService $broadcasts)
    {
        $data = $this->validate($this->rules());
        $payload = [
            'category_id' => (int) $data['category_id'],
            'name' => $data['name'],
            'price' => $data['price'],
        ];
        $tagIds = array_map('intval', $data['tags'] ?? []);

        if ($this->recordId === null) {
            $product = Product::query()->create($payload);
            $product->tags()->sync($tagIds);

            $broadcasts->broadcastAfterResponse(
                entity: 'product',
                action: 'created',
                id: $product->id,
                title: 'Товар создан',
                message: sprintf('Товар "%s" был создан.', $product->name),
                url: route('products.index'),
            );

            $this->dispatch('product-saved', status: 'Товар создан.');

            return;
        }

        $product = Product::query()->findOrFail($this->recordId);
        $product->update($payload);
        $product->tags()->sync($tagIds);

        $broadcasts->broadcastAfterResponse(
            entity: 'product',
            action: 'updated',
            id: $product->id,
            title: 'Товар обновлен',
            message: sprintf('Товар "%s" был обновлен.', $product->name),
            url: route('products.index'),
        );

        $this->dispatch('product-saved', status: 'Товар обновлен.');
    }

    public function cancel(): void
    {
        $this->dispatch('product-form-cancelled');
    }

    public function getCategoriesProperty()
    {
        return Category::query()->orderBy('name')->get();
    }

    public function getAvailableTagsProperty()
    {
        return Tag::query()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.products.form');
    }

    private function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'tags' => ['array'],
            'tags.*' => ['integer', 'distinct', 'exists:tags,id'],
        ];
    }
}
