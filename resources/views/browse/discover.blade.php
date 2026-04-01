<x-layouts.app :title="'LaraIMDb | Discover'">
    <h1>Discover</h1>
    <p class="muted">Filtered results for: {{ $query }}</p>

    <div class="chips">
        @foreach($filters as $key => $value)
            @if($value)
                <span class="chip">{{ ucfirst($key) }}: {{ $value }}</span>
            @endif
        @endforeach
    </div>

    <div class="grid">
        @foreach($results as $movie)
            @php($id = $movie['id'] ?? $movie['tconst'] ?? null)
            @continue(!$id)
            <article class="card">
                <a href="{{ route('movies.show', $id) }}">
                    <img src="{{ $movie['primaryImage'] ?? $movie['image']['url'] ?? 'https://placehold.co/300x450?text=No+Image' }}" alt="{{ $movie['primaryTitle'] ?? 'Poster' }}">
                    <h4>{{ $movie['primaryTitle'] ?? 'Untitled' }}</h4>
                </a>
            </article>
        @endforeach
    </div>
</x-layouts.app>
