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

    <h2 style="margin-top:30px">User Reviews</h2>
    <div class="card" style="margin-bottom:20px;">
        <h3>Write a review</h3>
        <form method="POST" action="{{ route('reviews.store', $movie['id'] ?? '') }}" style="display:grid;gap:10px;">
            @csrf
            <input type="text" name="user_name" placeholder="Your name (optional)">
            <input type="number" min="1" max="10" name="rating" placeholder="Rating 1-10 (optional)">
            <input type="text" name="headline" placeholder="Title / headline" required>
            <textarea name="body" placeholder="Write your review" rows="4" required></textarea>
            <textarea name="pros" placeholder="Pros (optional)" rows="2"></textarea>
            <textarea name="cons" placeholder="Cons (optional)" rows="2"></textarea>
            <label><input type="checkbox" name="is_spoiler" value="1"> Contains spoiler</label>
            <div style="display:flex;gap:10px;">
                <button type="submit" name="action" value="draft">Save Draft</button>
                <button type="submit" name="action" value="publish">Publish</button>
            </div>
        </form>
    </div>

    @forelse($reviews as $review)
        <div class="card" style="margin-bottom:16px;">
            <p><strong>{{ $review->headline }}</strong> <span class="muted">by {{ $review->user_name }} · {{ strtoupper($review->status) }}</span></p>
            @if($review->rating)
                <p class="muted">Rating: {{ $review->rating }}/10</p>
            @endif
            @if($review->is_spoiler)
                <p style="color:#ff9a9a;">Spoiler warning</p>
            @endif
            <p>{{ $review->body }}</p>
            @if($review->pros)
                <p><strong>Pros:</strong> {{ $review->pros }}</p>
            @endif
            @if($review->cons)
                <p><strong>Cons:</strong> {{ $review->cons }}</p>
            @endif

            <p class="muted">Helpful votes: {{ $review->helpfulVotes->count() }} · Abuse reports: {{ $review->abuseReports->count() }}</p>
            <div style="display:flex;gap:8px;flex-wrap:wrap; margin-bottom:10px;">
                <form method="POST" action="{{ route('reviews.helpful', $review) }}">@csrf<button type="submit">👍 Helpful</button></form>
                <form method="POST" action="{{ route('reviews.report', $review) }}" style="display:flex;gap:8px;">
                    @csrf
                    <input type="text" name="reason" placeholder="Report reason (optional)">
                    <button type="submit">Report abuse</button>
                </form>
                <form method="POST" action="{{ route('reviews.destroy', $review) }}">@csrf @method('DELETE')<button type="submit">Delete</button></form>
            </div>

            <details>
                <summary>Edit review</summary>
                <form method="POST" action="{{ route('reviews.update', $review) }}" style="display:grid;gap:8px;margin-top:10px;">
                    @csrf
                    @method('PUT')
                    <input type="text" name="user_name" value="{{ $review->user_name }}">
                    <input type="number" min="1" max="10" name="rating" value="{{ $review->rating }}">
                    <input type="text" name="headline" value="{{ $review->headline }}" required>
                    <textarea name="body" rows="3" required>{{ $review->body }}</textarea>
                    <textarea name="pros" rows="2">{{ $review->pros }}</textarea>
                    <textarea name="cons" rows="2">{{ $review->cons }}</textarea>
                    <label><input type="checkbox" name="is_spoiler" value="1" @checked($review->is_spoiler)> Contains spoiler</label>
                    <div style="display:flex;gap:10px;">
                        <button type="submit" name="action" value="draft">Unpublish (save draft)</button>
                        <button type="submit" name="action" value="publish">Publish</button>
                    </div>
                </form>
            </details>

            <h4 style="margin-top:12px;">Comments</h4>
            @forelse($review->comments as $comment)
                <p class="muted" style="margin:6px 0;"><strong>{{ $comment->user_name }}:</strong> {{ $comment->body }}</p>
            @empty
                <p class="muted">No comments yet.</p>
            @endforelse
            <form method="POST" action="{{ route('reviews.comments.store', $review) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                @csrf
                <input type="text" name="user_name" placeholder="Name (optional)">
                <input type="text" name="body" placeholder="Add a comment" required>
                <button type="submit">Comment</button>
            </form>
        </div>
    @empty
        <p class="muted">No reviews yet.</p>
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
