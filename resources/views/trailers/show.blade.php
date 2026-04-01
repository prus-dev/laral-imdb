<x-layouts.app :title="'LaraIMDb | Trailer'">
    <h1>{{ $movie['primaryTitle'] ?? 'Trailer' }}</h1>
    <p class="muted">Trailer page experience (modal-style destination fallback).</p>
    <img style="max-width:300px;border-radius:8px" src="{{ $movie['primaryImage'] ?? $movie['image']['url'] ?? 'https://placehold.co/300x450?text=Trailer' }}" alt="Trailer poster">
    @if(!empty($movie['url']))
        <p><a href="{{ $movie['url'] }}" target="_blank" rel="noopener">Watch on source</a></p>
    @endif
</x-layouts.app>
