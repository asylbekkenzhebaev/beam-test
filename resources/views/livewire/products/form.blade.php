<form wire:submit="save">
    <div class="grid gap-5 md:grid-cols-2">
        <label class="block">
            <span class="mb-2 block text-sm font-medium text-stone-700">Название</span>
            <input type="text" wire:model="name" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 outline-none transition focus:border-stone-900" required>
            @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        </label>

        <label class="block">
            <span class="mb-2 block text-sm font-medium text-stone-700">Цена</span>
            <input type="number" wire:model="price" min="0.01" step="0.01" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 outline-none transition focus:border-stone-900" required>
            @error('price')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        </label>
    </div>

    <label class="mt-5 block">
        <span class="mb-2 block text-sm font-medium text-stone-700">Категория</span>
        <select wire:model="category_id" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 outline-none transition focus:border-stone-900" required>
            <option value="">Выберите категорию</option>
            @foreach ($this->categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        @if ($this->categories->isEmpty())
            <p class="mt-2 text-sm text-amber-700">Сначала создайте хотя бы одну категорию, чтобы добавить товар.</p>
        @endif
    </label>

    <fieldset class="mt-5">
        <legend class="mb-3 text-sm font-medium text-stone-700">Теги</legend>
        <div class="grid gap-3 sm:grid-cols-2">
            @forelse ($this->availableTags as $tag)
                <label class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                    <input type="checkbox" wire:model="tags" value="{{ $tag->id }}" class="rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                    <span class="text-sm text-stone-700">{{ $tag->name }}</span>
                </label>
            @empty
                <p class="text-sm text-stone-500">Тегов пока нет. Товар можно сохранить и без тегов.</p>
            @endforelse
        </div>
        @error('tags.*')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </fieldset>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="rounded-full bg-stone-900 px-5 py-2.5 text-sm font-medium text-white" @disabled($this->categories->isEmpty())>{{ $recordId ? 'Обновить товар' : 'Создать товар' }}</button>
        <button type="button" wire:click="cancel" class="text-sm text-stone-500">Отмена</button>
    </div>
</form>
