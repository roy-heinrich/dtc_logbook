@props(['showHeader' => true])

<div
    x-data="{ loading: true }"
    x-init="$nextTick(() => { loading = false })"
    class="space-y-6"
>
    <!-- Show skeleton header while loading -->
    @if ($showHeader)
        <template x-if="loading">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <div class="skeleton h-8 w-48"></div>
                    <div class="skeleton h-4 w-96"></div>
                </div>
                <div class="skeleton h-10 w-32"></div>
            </div>
        </template>
    @endif

    <!-- Content container -->
    <div x-show="!loading" x-cloak>
        {{ $slot }}
    </div>
</div>
