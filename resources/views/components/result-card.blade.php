@props(['result' => '', 'emoji' => '✨', 'loading' => false])

<div class="relative bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 my-6">

    @if($loading)
    <div class="flex items-center gap-3 text-zinc-500">
        <div class="flex gap-1">
            <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
            <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
            <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
        </div>
        <span class="text-sm">A gerar...</span>
    </div>
    @else
    <div class="text-3xl mb-3">{{ $emoji }}</div>
    <p class="text-lg font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed">
        {{ $result }}
    </p>
    @endif

    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
        <a href="https://{{ config('app.base_domain') }}" class="text-xs text-zinc-400 hover:text-zinc-500">clinky.cc</a>
    </div>
</div>
