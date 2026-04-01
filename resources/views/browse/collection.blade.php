<x-layouts.app :title="'LaraIMDb | '.$heading">
    <h1>{{ $heading }}</h1>
    <p class="muted">Full listing view with quick sorting.</p>

    <form method="GET" class="filterbar">
        <label for="sort">Sort</label>
        <select name="sort" id="sort">
            <option value="">Default</option>
            <option value="year_desc" @selected(request('sort') === 'year_desc')>Release year (newest)</option>
        </select>
        <button type="submit">Apply</button>
    </form>

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
