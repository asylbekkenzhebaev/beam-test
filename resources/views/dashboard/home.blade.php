@extends('layouts.main', ['title' => 'Главная'])

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('users.index') }}"
           class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm shadow-stone-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm uppercase tracking-[0.25em] text-stone-400">Пользователи</p>
            <p class="mt-3 text-lg font-semibold text-stone-900">Управление пользователями каталога</p>
        </a>
        <a href="{{ route('categories.index') }}"
           class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm shadow-stone-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm uppercase tracking-[0.25em] text-stone-400">Категории</p>
            <p class="mt-3 text-lg font-semibold text-stone-900">Структура и разделы каталога</p>
        </a>
        <a href="{{ route('products.index') }}"
           class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm shadow-stone-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm uppercase tracking-[0.25em] text-stone-400">Товары</p>
            <p class="mt-3 text-lg font-semibold text-stone-900">Товары, категории и теги</p>
        </a>
        <a href="{{ route('tags.index') }}"
           class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm shadow-stone-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm uppercase tracking-[0.25em] text-stone-400">Теги</p>
            <p class="mt-3 text-lg font-semibold text-stone-900">Гибкие метки для товаров</p>
        </a>
    </section>
@endsection
