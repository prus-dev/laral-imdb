<x-layouts.app :title="'Search | LaraIMDb'">
    <h1>Search Titles</h1>
    <form method="GET" action="{{ route('movies.search') }}" style="margin-bottom:20px;display:flex;gap:10px">
        <input type="text" name="q" value="{{ $query }}" placeholder="Search movie, series, cast..." style="flex:1">
        <button type="submit">Search</button>
    </form>

    <div class="grid">
        @foreach($results as $movie)
            @php($id = $movie['id'] ?? $movie['tconst'] ?? null)
            @continue(!$id)
            <div class="card">
                <a href="{{ in_array(strtolower($movie['titleType'] ?? $movie['type'] ?? ''), ['tvseries','tv series','series']) ? route('series.show', $id) : route('movies.show', $id) }}">
                    <img src="{{ $movie['primaryImage'] ?? $movie['image']['url'] ?? 'https://placehold.co/300x450?text=No+Image' }}" alt="{{ $movie['primaryTitle'] ?? 'Poster' }}">
                    <h4>{{ $movie['primaryTitle'] ?? 'Untitled' }}</h4>
                </a>
                <p class="muted">{{ $movie['titleType'] ?? $movie['type'] ?? 'Title' }} · {{ $movie['startYear'] ?? '—' }}</p>
            </div>
        @endforeach
    </div>
</x-layouts.app>
