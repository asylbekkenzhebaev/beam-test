<section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm shadow-stone-200/70">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold">Товары</h2>
            <p class="text-sm text-stone-500">Управляйте товарами, категориями и тегами.</p>
        </div>
        <button type="button" wire:click="create" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-medium text-white">Создать товар</button>
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
                    <th class="pb-3">Категория</th>
                    <th class="pb-3">Цена</th>
                    <th class="pb-3">Теги</th>
                    <th class="pb-3 text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($products as $product)
                    <tr>
                        <td class="py-4 font-medium">{{ $product->name }}</td>
                        <td class="py-4 text-stone-600">{{ $product->category->name }}</td>
                        <td class="py-4 text-stone-600">${{ number_format((float) $product->price, 2) }}</td>
                        <td class="py-4 text-stone-600">{{ $product->tags->pluck('name')->join(', ') ?: 'Без тегов' }}</td>
                        <td class="py-4">
                            <div class="flex justify-end gap-2">
                                <button type="button" wire:click="edit({{ $product->id }})" class="rounded-full bg-stone-100 px-3 py-1.5 text-stone-700">Редактировать</button>
                                <button type="button" wire:click="delete({{ $product->id }})" wire:confirm="Удалить этот товар?" class="rounded-full bg-rose-100 px-3 py-1.5 text-rose-700">Удалить</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-stone-500">Товаров пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showFormModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-stone-950/45 px-4 py-6" wire:click="closeModal">
            <div class="w-full max-w-4xl rounded-[2rem] bg-white p-6 shadow-2xl" wire:click.stop>
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-stone-900">{{ $editingId ? 'Редактировать товар' : 'Создать товар' }}</h3>
                        <p class="mt-1 text-sm text-stone-500">Создавайте и обновляйте товары прямо в списке.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="rounded-full bg-stone-100 px-3 py-1.5 text-sm text-stone-600">Закрыть</button>
                </div>

                @livewire('products.form', ['recordId' => $editingId], key('products-form-' . ($editingId ?? 'new')))
            </div>
        </div>
    @endif
</section>
