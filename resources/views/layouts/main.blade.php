<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $appName = config('app.name');
    @endphp
    <title>{{ ($title ?? $appName) . ' | ' . $appName }}</title>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    @php
        $catalogPusherConfig = [
            'key' => config('services.pusher.frontend_key'),
            'cluster' => config('services.pusher.frontend_cluster'),
            'channel' => \App\Services\EntityBroadcastService::CHANNEL,
            'event' => \App\Services\EntityBroadcastService::EVENT,
        ];
    @endphp
    <script>
        window.catalogPusherConfig = {{ Js::from($catalogPusherConfig) }};
    </script>
    <script>
        (() => {
            const mutedMessages = [
                '[vite] connecting...',
                '[vite] connected.',
                '🔍 Browser logger active (MCP server detected).',
            ];

            const shouldMute = (args) => typeof args[0] === 'string'
                && mutedMessages.some((message) => args[0].includes(message));

            for (const method of ['log', 'info', 'debug']) {
                const original = console[method];

                console[method] = (...args) => {
                    if (shouldMute(args)) {
                        return;
                    }

                    original.apply(console, args);
                };
            }
        })();
    </script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-900">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-56 bg-gradient-to-r from-amber-300 via-orange-200 to-lime-200"></div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <header class="mb-6 rounded-[2rem] border border-white/70 bg-white/80 px-6 py-5 shadow-lg shadow-stone-200/60 backdrop-blur">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <a href="{{ route('home') }}" class="text-sm font-semibold uppercase tracking-[0.35em] text-stone-500">{{ $appName }}</a>
                        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900">{{ $title ?? 'Каталог' }}</h1>
                    </div>

                    <nav class="flex flex-wrap gap-2 text-sm font-medium">
                        <a href="{{ route('users.index') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('users.*') ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">Пользователи</a>
                        <a href="{{ route('categories.index') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('categories.*') ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">Категории</a>
                        <a href="{{ route('products.index') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('products.*') ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">Товары</a>
                        <a href="{{ route('tags.index') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('tags.*') ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">Теги</a>
                    </nav>
                </div>
            </header>

            {{-- Flash-сообщения остаются для текущей вкладки после create/update. --}}
            @if (session('status'))
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot ?? '' }}

            @yield('content')
        </div>
    </div>

    {{-- Pusher-тосты нужны в основном для других открытых вкладок, которые не перезагружаются после сохранения формы. --}}
    <div id="catalog-toast-region" class="pointer-events-none fixed right-4 top-4 z-50 flex w-[min(22rem,calc(100%-2rem))] flex-col gap-3 sm:right-6" aria-live="polite" aria-atomic="true"></div>
    @livewireScripts
</body>
</html>
