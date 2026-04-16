@props(['emoji', 'title', 'tagline', 'accentColor' => 'lime'])

<header class="text-center py-12 px-4">
    <div class="text-6xl mb-4">{{ $emoji }}</div>
    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
        {{ $title }}
    </h1>
    <p class="mt-2 text-zinc-500 dark:text-zinc-400 text-sm max-w-xs mx-auto">
        {{ $tagline }}
    </p>
</header>
