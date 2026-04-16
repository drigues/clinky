@props(['count', 'label' => 'vezes'])

<div class="inline-flex items-center gap-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs font-medium px-3 py-1.5 rounded-full">
    <span class="w-1.5 h-1.5 bg-lime-500 rounded-full animate-pulse"></span>
    <span x-data="{ n: 0, target: {{ $count }} }"
          x-init="let s=setInterval(()=>{ n=Math.min(n+Math.ceil(target/40), target); if(n>=target) clearInterval(s); }, 40)">
        <span x-text="n.toLocaleString('pt-PT')"></span>
    </span>
    {{ $label }}
</div>
