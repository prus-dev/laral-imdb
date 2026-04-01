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

    <h2 style="margin-top:30px">Review Integrity Features</h2>
    <ul class="muted" style="line-height:1.7">
        <li>Profanity moderation</li>
        <li>Spam detection</li>
        <li>Duplicate detection</li>
        <li>AI-generated spam detection (optional)</li>
        <li>Review quality scoring</li>
        <li>Rate limiting</li>
        <li>Moderator queue</li>
    </ul>

    <section class="card" style="margin:20px 0">
        <h3>Write a Review</h3>
        <form method="POST" action="{{ route('reviews.store') }}" style="display:grid;gap:10px">
            @csrf
            <input type="hidden" name="movie_imdb_id" value="{{ $movie['id'] ?? '' }}">
            <input type="hidden" name="movie_title" value="{{ $movie['primaryTitle'] ?? '' }}">
            <input type="text" name="author_name" placeholder="Your name (optional)">
            <textarea name="content" rows="5" minlength="10" maxlength="2000" placeholder="Share what you thought..." required></textarea>
            <button type="submit" style="width:max-content">Submit Review</button>
        </form>
    </section>

    <h3>Approved Reviews</h3>
    @forelse($reviews as $review)
        <article class="card" style="margin-bottom:12px">
            <p style="margin:0 0 8px">{{ $review->content }}</p>
            <p class="muted" style="margin:0">By {{ $review->author_name ?: 'Anonymous' }} · Quality: {{ $review->quality_score }}/100</p>
        </article>
    @empty
        <p class="muted">No approved reviews yet.</p>
    @endforelse

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
</x-layouts.app>
