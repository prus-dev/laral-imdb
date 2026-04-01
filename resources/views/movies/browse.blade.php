<x-layouts.app :title="'Browse | LaraIMDb'">
    <h1>Browse titles</h1>
    <p class="muted">Sorting: <strong>{{ ucfirst($sort) }}</strong></p>

    @if($filters)
        <div class="grid quick-grid" style="margin:14px 0 18px;">
            @foreach($filters as $key => $value)
                <span class="quick-pill">{{ ucfirst($key) }}: {{ $value }}</span>
            @endforeach
        </div>
    @endif

    <div class="grid">
        @foreach($results as $movie)
            @php($id = $movie['id'] ?? $movie['tconst'] ?? null)
            @continue(!$id)
            <article class="card">
                <a href="{{ route('movies.show', $id) }}">
                    <img src="{{ $movie['primaryImage'] ?? $movie['image']['url'] ?? 'https://placehold.co/300x450?text=No+Image' }}" alt="{{ $movie['primaryTitle'] ?? 'Poster' }}">
                    <h4>{{ $movie['primaryTitle'] ?? 'Untitled' }}</h4>
                </a>
                <p class="muted">⭐ {{ $movie['averageRating'] ?? 'N/A' }} · {{ $movie['startYear'] ?? '—' }}</p>
            </article>
        @endforeach
    </div>
</x-layouts.app>
