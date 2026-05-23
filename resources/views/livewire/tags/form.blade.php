<form wire:submit="save">
    <label class="block">
        <span class="mb-2 block text-sm font-medium text-stone-700">Название</span>
        <input type="text" wire:model="name" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 outline-none ring-0 transition focus:border-stone-900" required>
        @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </label>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="rounded-full bg-stone-900 px-5 py-2.5 text-sm font-medium text-white">{{ $recordId ? 'Обновить тег' : 'Создать тег' }}</button>
        <button type="button" wire:click="cancel" class="text-sm text-stone-500">Отмена</button>
    </div>
</form>
