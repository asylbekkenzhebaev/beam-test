<section
    x-data
    x-on:catalog-entity-changed.window="if ($event.detail.entity === 'tag') { $wire.refreshFromBroadcast() }"
    class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm shadow-stone-200/70"
>
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold">Теги</h2>
            <p class="text-sm text-stone-500">Группируйте товары с помощью гибких тегов.</p>
        </div>
        <button type="button" wire:click="create" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-medium text-white">Создать тег</button>
    </div>

    @if ($statusMessage)
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ $statusMessage }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ $errorMessage }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="text-stone-500">
                <tr>
                    <th class="pb-3">Название</th>
                    <th class="pb-3">Товары</th>
                    <th class="pb-3 text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($tags as $tag)
                    <tr>
                        <td class="py-4 font-medium">{{ $tag->name }}</td>
                        <td class="py-4 text-stone-600">{{ $tag->products_count }}</td>
                        <td class="py-4">
                            <div class="flex justify-end gap-2">
                                <button type="button" wire:click="edit({{ $tag->id }})" class="rounded-full bg-stone-100 px-3 py-1.5 text-stone-700">Редактировать</button>
                                <button type="button" wire:click="delete({{ $tag->id }})" wire:confirm="Удалить этот тег?" class="rounded-full bg-rose-100 px-3 py-1.5 text-rose-700">Удалить</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-stone-500">Тегов пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showFormModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-stone-950/45 px-4 py-6" wire:click="closeModal">
            <div class="w-full max-w-2xl rounded-[2rem] bg-white p-6 shadow-2xl" wire:click.stop>
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-stone-900">{{ $editingId ? 'Редактировать тег' : 'Создать тег' }}</h3>
                        <p class="mt-1 text-sm text-stone-500">Создавайте и обновляйте теги прямо в списке.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="rounded-full bg-stone-100 px-3 py-1.5 text-sm text-stone-600">Закрыть</button>
                </div>

                @livewire('tags.form', ['recordId' => $editingId], key('tags-form-' . ($editingId ?? 'new')))
            </div>
        </div>
    @endif
</section>
