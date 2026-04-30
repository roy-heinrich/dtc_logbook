@props(['count' => 1])

<div class="contents">
    @for ($i = 0; $i < $count; $i++)
        <div class="rounded-lg glass-card p-5 h-full min-h-[132px] flex flex-col justify-between shadow-sm">
            <div class="skeleton-text h-4 w-32 rounded mb-2"></div>
            <div class="skeleton-text h-8 w-24 rounded mt-3"></div>
        </div>
    @endfor
</div>
