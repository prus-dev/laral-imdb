<x-layouts.app :title="'Watchlist | LaraIMDb'">
    <h1>Your Watchlist</h1>
    <div class="grid">
        @forelse($items as $item)
            <div class="card">
                <img src="{{ $item->poster_url ?? 'https://placehold.co/300x450?text=No+Image' }}" alt="{{ $item->title }}">
                <h4><a href="{{ str_contains(strtolower((string) $item->type), 'series') ? route('series.show', $item->imdb_id) : route('movies.show', $item->imdb_id) }}">{{ $item->title }}</a></h4>
                <p class="muted">⭐ {{ $item->rating ?? 'N/A' }} · {{ $item->year ?? '—' }}</p>
                <form method="POST" action="{{ route('watchlist.destroy', $item) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Remove</button>
                </form>
            </div>
        @empty
            <p class="muted">No watchlist items yet.</p>
        @endforelse
    </div>
</x-layouts.app>
