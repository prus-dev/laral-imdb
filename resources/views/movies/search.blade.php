<x-layouts.app :title="$title ?? 'Search | LaraIMDb'">
    <livewire:movie-search :q="request('q')" />
</x-layouts.app>
