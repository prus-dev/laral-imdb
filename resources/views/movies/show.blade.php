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

            <h3>Rate this title</h3>
            @if (session('status'))
                <p class="muted">{{ session('status') }}</p>
            @endif
            <form method="POST" action="{{ route('movies.rate', $movie['id']) }}" style="display:flex;gap:10px;align-items:center;margin-bottom:10px">
                @csrf
                <label for="score">Your rating (1-10)</label>
                <input id="score" type="number" name="score" min="1" max="10" required style="width:70px">
                <button type="submit">Submit</button>
            </form>
            <p class="muted">
                Avg: <strong>{{ $ratingInsights['average_score'] }}</strong>
                · Votes: <strong>{{ $ratingInsights['total_votes'] }}</strong>
                · Weighted rank: <strong>{{ $ratingInsights['weighted_ranking'] }}</strong>
            </p>
            <p class="muted">
                Public ranking: <strong>{{ $ratingInsights['public_ranking_eligible'] ? 'Eligible' : 'Protected/Hidden' }}</strong>
                (min {{ $ratingInsights['minimum_votes_threshold'] }} votes, {{ $ratingInsights['new_title_min_votes'] }} for new titles)
            </p>
        </div>
    </div>

    <h2 style="margin-top:30px">Rating Insights</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">
        <div>
            <h3>Histogram by score</h3>
            @php($maxBucket = max($ratingInsights['histogram']) ?: 1)
            @foreach($ratingInsights['histogram'] as $score => $votes)
                <div style="display:grid;grid-template-columns:40px 1fr 50px;gap:8px;align-items:center;margin-bottom:6px">
                    <span>{{ $score }}</span>
                    <div style="height:8px;background:#20263a;border-radius:4px;overflow:hidden">
                        <div style="height:8px;background:#4f8cff;width:{{ ($votes / $maxBucket) * 100 }}%"></div>
                    </div>
                    <span>{{ $votes }}</span>
                </div>
            @endforeach

            <h3 style="margin-top:20px">Rating trend (14 days)</h3>
            @forelse($ratingInsights['trend'] as $point)
                <p class="muted">{{ $point['day'] }} — Avg {{ $point['avg_score'] }} ({{ $point['votes'] }} votes)</p>
            @empty
                <p class="muted">Not enough recent votes yet.</p>
            @endforelse
        </div>

        <div>
            <h3>Personal vs community</h3>
            <p class="muted">Your latest rating: <strong>{{ $ratingInsights['personal_rating'] ?? '—' }}</strong></p>
            <p class="muted">Community average: <strong>{{ $ratingInsights['community_average'] }}</strong></p>

            <h3 style="margin-top:20px">Demographic breakdown (optional)</h3>
            @forelse($ratingInsights['demographic_breakdown'] as $country)
                <p class="muted">{{ $country['country'] }}: {{ $country['votes'] }} votes</p>
            @empty
                <p class="muted">No country-level signal yet.</p>
            @endforelse

            <h3 style="margin-top:20px">Top liked reviews vs score distribution</h3>
            @foreach($ratingInsights['top_liked_reviews_proxy'] as $bin)
                <p class="muted">Score {{ $bin['score'] }} has {{ $bin['votes'] }} votes (proxy for sentiment peaks)</p>
            @endforeach

            <h3 style="margin-top:20px">Abuse signals</h3>
            <p class="muted">Suspicious votes flagged: {{ $ratingInsights['abuse_signals']['suspicious_votes'] }}</p>
            <p class="muted">Distinct devices: {{ $ratingInsights['abuse_signals']['devices'] }} · Distinct accounts: {{ $ratingInsights['abuse_signals']['accounts'] }}</p>
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
</x-layouts.app>
