<x-layouts.app title="Moderator Queue | LaraIMDb">
    <h1>Moderator Queue</h1>

    @forelse($reviews as $review)
        <article class="card" style="margin-bottom:12px">
            <h3>{{ $review->movie_title ?: $review->movie_imdb_id }}</h3>
            <p class="muted">By {{ $review->author_name ?: 'Anonymous' }} · Quality: {{ $review->quality_score }}/100</p>
            @if(!empty($review->flags))
                <p class="muted">Flags: {{ implode(', ', $review->flags) }}</p>
            @endif
            <p>{{ $review->content }}</p>
            <div style="display:flex;gap:8px">
                <form method="POST" action="{{ route('moderation.reviews.approve', $review) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Approve</button>
                </form>
                <form method="POST" action="{{ route('moderation.reviews.reject', $review) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="background:#cc3f3f;color:#fff">Reject</button>
                </form>
            </div>
        </article>
    @empty
        <p class="muted">No pending reviews.</p>
    @endforelse
</x-layouts.app>
