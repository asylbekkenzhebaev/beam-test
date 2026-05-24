<form wire:submit="save">
    <div class="grid gap-5 md:grid-cols-2">
        <label class="block">
            <span class="mb-2 block text-sm font-medium text-stone-700">Имя</span>
            <input type="text" wire:model="name" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 outline-none ring-0 transition focus:border-stone-900" required>
            @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        </label>

        <label class="block">
            <span class="mb-2 block text-sm font-medium text-stone-700">Email</span>
            <input type="email" wire:model="email" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 outline-none ring-0 transition focus:border-stone-900" required>
            @error('email')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        </label>

        <label class="block md:col-span-2">
            <span class="mb-2 block text-sm font-medium text-stone-700">Телефон</span>
            <input type="text" wire:model="phone" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 outline-none ring-0 transition focus:border-stone-900" placeholder="+996 555 123 456">
            @error('phone')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        </label>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="rounded-full bg-stone-900 px-5 py-2.5 text-sm font-medium text-white">{{ $recordId ? 'Обновить пользователя' : 'Создать пользователя' }}</button>
        <button type="button" wire:click="cancel" class="text-sm text-stone-500">Отмена</button>
    </div>
</form>
