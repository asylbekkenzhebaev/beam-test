<?php

namespace App\Livewire\Tags;

use App\Models\Tag;
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

        $tag = Tag::query()->findOrFail($recordId);

        $this->name = $tag->name;
    }

    public function save(EntityBroadcastService $broadcasts)
    {
        $data = $this->validate($this->rules());

        if ($this->recordId === null) {
            $tag = Tag::query()->create($data);

            $broadcasts->broadcastAfterResponse(
                entity: 'tag',
                action: 'created',
                id: $tag->id,
                title: 'Тег создан',
                message: sprintf('Тег "%s" был создан.', $tag->name),
                url: route('tags.index'),
            );

            $this->dispatch('tag-saved', status: 'Тег создан.');

            return;
        }

        $tag = Tag::query()->findOrFail($this->recordId);
        $tag->update($data);

        $broadcasts->broadcastAfterResponse(
            entity: 'tag',
            action: 'updated',
            id: $tag->id,
            title: 'Тег обновлен',
            message: sprintf('Тег "%s" был обновлен.', $tag->name),
            url: route('tags.index'),
        );

        $this->dispatch('tag-saved', status: 'Тег обновлен.');
    }

    public function cancel(): void
    {
        $this->dispatch('tag-form-cancelled');
    }

    public function render()
    {
        return view('livewire.tags.form');
    }

    private function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags', 'name')->ignore($this->recordId),
            ],
        ];
    }
}
