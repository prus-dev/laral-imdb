<x-layouts.app :title="'LaraIMDb | Trending'">
    <h1>Trending</h1>
    <div class="grid">
        @foreach($trending as $movie)
            @php($id = $movie['id'] ?? $movie['tconst'] ?? null)
            @continue(!$id)
            <div class="card">
                <a href="{{ in_array(strtolower($movie['titleType'] ?? $movie['type'] ?? ''), ['tvseries','tv series','series']) ? route('series.show', $id) : route('movies.show', $id) }}">
                    <img src="{{ $movie['primaryImage'] ?? $movie['image']['url'] ?? 'https://placehold.co/300x450?text=No+Image' }}" alt="{{ $movie['primaryTitle'] ?? 'Poster' }}">
                    <h4>{{ $movie['primaryTitle'] ?? 'Untitled' }}</h4>
                </a>
                <p class="muted">⭐ {{ $movie['averageRating'] ?? 'N/A' }} · {{ $movie['startYear'] ?? '—' }}</p>
            </div>
        @endforeach
    </div>
</x-layouts.app>
