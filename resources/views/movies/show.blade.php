<x-layouts.app :title="($movie['primaryTitle'] ?? 'Title').' | LaraIMDb'">
    <div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">
        <img src="{{ $movie['primaryImage'] ?? 'https://placehold.co/400x600?text=No+Image' }}" alt="Poster" style="width:100%;border-radius:8px">
        <div>
            <h1>{{ $movie['primaryTitle'] ?? 'Untitled' }}</h1>
            <p class="muted">{{ $movie['startYear'] ?? '—' }} · {{ $movie['runtimeMinutes'] ?? '—' }} min · ⭐ {{ $movie['averageRating'] ?? 'N/A' }}</p>
            <p>{{ $movie['description'] ?? $movie['plot'] ?? 'No plot available.' }}</p>

            <form method="POST" action="{{ route('watchlist.store') }}" style="margin:15px 0">
                @csrf
                <input type="hidden" name="imdb_id" value="{{ $movie['id'] ?? '' }}">
                <input type="hidden" name="title" value="{{ $movie['primaryTitle'] ?? '' }}">
                <input type="hidden" name="year" value="{{ $movie['startYear'] ?? '' }}">
                <input type="hidden" name="poster_url" value="{{ $movie['primaryImage'] ?? '' }}">
                <input type="hidden" name="rating" value="{{ $movie['averageRating'] ?? '' }}">
                <input type="hidden" name="runtime_seconds" value="{{ isset($movie['runtimeMinutes']) ? (int)$movie['runtimeMinutes'] * 60 : '' }}">
                <input type="hidden" name="type" value="{{ $movie['titleType'] ?? '' }}">
                <button type="submit">+ Add to Watchlist</button>
            </form>
        </div>
    </div>

    <h2 style="margin-top:30px">More Like This</h2>
    <div class="grid">
        @foreach($similar as $item)
            @php($id = $item['id'] ?? $item['tconst'] ?? null)
            @continue(!$id)
            <div class="card">
                <a href="{{ route('movies.show', $id) }}">
                    <img src="{{ $item['primaryImage'] ?? 'https://placehold.co/300x450?text=No+Image' }}" alt="Poster">
                    <h4>{{ $item['primaryTitle'] ?? 'Untitled' }}</h4>
                </a>
            </div>
        @endforeach
    </div>

    <h2 style="margin-top:30px">Reviews</h2>
    <form method="GET" action="{{ route('movies.show', $movie['id'] ?? '') }}" style="margin:12px 0;display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;align-items:end">
        <label>
            Sort
            <select name="sort">
                <option value="most_helpful" @selected(($reviewFilters['sort'] ?? 'most_helpful') === 'most_helpful')>Most helpful</option>
                <option value="newest" @selected(($reviewFilters['sort'] ?? '') === 'newest')>Newest</option>
                <option value="highest_rated_reviewer" @selected(($reviewFilters['sort'] ?? '') === 'highest_rated_reviewer')>Highest rated reviewer</option>
            </select>
        </label>
        <label>
            Min score
            <input type="number" name="score_min" min="0" max="10" step="0.1" value="{{ $reviewFilters['score_min'] ?? '' }}">
        </label>
        <label>
            Max score
            <input type="number" name="score_max" min="0" max="10" step="0.1" value="{{ $reviewFilters['score_max'] ?? '' }}">
        </label>
        <label>
            <input type="checkbox" name="spoilers_only" value="1" @checked($reviewFilters['spoilers_only'] ?? false)>
            Spoilers only
        </label>
        <label>
            <input type="checkbox" name="verified_only" value="1" @checked($reviewFilters['verified_only'] ?? false)>
            Verified watchers only
        </label>
        <button type="submit">Apply</button>
    </form>

    <div style="display:grid;gap:12px">
        @forelse($reviews as $review)
            <article class="card" style="padding:14px">
                <h3 style="margin:0 0 6px">{{ $review['title'] ?? 'User review' }}</h3>
                <p class="muted" style="margin:0 0 8px">
                    {{ $review['author'] ?? 'Anonymous' }}
                    @if(!empty($review['date']) && strtotime((string) $review['date'])) · {{ date('M j, Y', strtotime((string) $review['date'])) }} @endif
                    @if(is_numeric($review['score'])) · Score: {{ $review['score'] }}/10 @endif
                    @if(($review['verified_watcher'] ?? false)) · ✅ Verified watcher @endif
                    @if(($review['spoiler'] ?? false)) · ⚠️ Spoiler @endif
                </p>
                <p style="margin:0">{{ $review['content'] ?? 'No review text provided.' }}</p>
                <small class="muted">Helpful votes: {{ $review['helpful_votes'] ?? 0 }}</small>
            </article>
        @empty
            <p class="muted">No reviews match your current filters.</p>
        @endforelse
    </div>
</x-layouts.app>
