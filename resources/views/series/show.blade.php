<x-layouts.app :title="($series['primaryTitle'] ?? 'Series').' | Series | LaraIMDb'">
    <h1>{{ $series['primaryTitle'] ?? 'Untitled Series' }}</h1>
    <p class="muted">
        ⭐ <span id="series-rating-value">{{ $series['averageRating'] ?? 'N/A' }}</span>
        · {{ count($episodes) }} episodes
        · {{ $series['startYear'] ?? '—' }}{{ isset($series['endYear']) ? ' - '.$series['endYear'] : '' }}
        · {{ $series['status'] ?? 'Unknown status' }}
    </p>
    <p>{{ $series['description'] ?? $series['plot'] ?? 'No synopsis available.' }}</p>
    <p class="muted">Creators: {{ collect($series['creators'] ?? [])->pluck('name')->filter()->join(', ') ?: 'N/A' }} · Network: {{ $series['network'] ?? $series['company'] ?? 'N/A' }}</p>

    <div class="card" style="margin:16px 0">
        <strong>Rate this series:</strong>
        <div style="display:flex;gap:8px;margin-top:10px">
            @for($i = 1; $i <= 10; $i++)
                <button type="button" class="rate-btn" data-rating="{{ $i }}">{{ $i }}</button>
            @endfor
        </div>
    </div>

    <h2>Seasons</h2>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px">
        @foreach($seasons as $season)
            @php($number = $season['seasonNumber'] ?? $season['number'] ?? null)
            @continue(!$number)
            <a href="{{ route('series.season', ['id' => $series['id'], 'season' => $number]) }}" class="card" style="padding:8px 12px">Season {{ $number }}</a>
        @endforeach
    </div>

    <h2>Episode release calendar</h2>
    <div class="card">
        @foreach(collect($episodes)->sortBy('releaseDate')->take(10) as $episode)
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #2c2f3a">
                <a href="{{ route('series.episode', ['id' => $series['id'], 'season' => $episode['seasonNumber'] ?? 1, 'episodeId' => $episode['id'] ?? $episode['tconst'] ?? '']) }}">{{ $episode['primaryTitle'] ?? 'Episode' }}</a>
                <span class="muted">{{ $episode['releaseDate'] ?? 'TBA' }}</span>
            </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:20px">
        <div class="card">
            <h3>Top rated episodes</h3>
            @foreach($topEpisodes as $episode)
                <p><a href="{{ route('series.episode', ['id' => $series['id'], 'season' => $episode['seasonNumber'] ?? 1, 'episodeId' => $episode['id'] ?? $episode['tconst'] ?? '']) }}">{{ $episode['primaryTitle'] ?? 'Episode' }}</a> <span class="muted">({{ $episode['averageRating'] ?? 'N/A' }})</span></p>
            @endforeach
        </div>
        <div class="card">
            <h3>Lowest rated episodes</h3>
            @foreach($lowestEpisodes as $episode)
                <p><a href="{{ route('series.episode', ['id' => $series['id'], 'season' => $episode['seasonNumber'] ?? 1, 'episodeId' => $episode['id'] ?? $episode['tconst'] ?? '']) }}">{{ $episode['primaryTitle'] ?? 'Episode' }}</a> <span class="muted">({{ $episode['averageRating'] ?? 'N/A' }})</span></p>
            @endforeach
        </div>
        <div class="card">
            <h3>Season ranking</h3>
            @foreach($seasonRanking as $season)
                <p><a href="{{ route('series.season', ['id' => $series['id'], 'season' => $season['season']]) }}">Season {{ $season['season'] }}</a> <span class="muted">({{ $season['rating'] }}, {{ $season['episodes'] }} eps)</span></p>
            @endforeach
        </div>
    </div>

    <script>
        document.querySelectorAll('.rate-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const rating = Number(button.dataset.rating);
                const existing = Number(document.getElementById('series-rating-value').textContent);
                if (!Number.isNaN(existing) && existing > 0) {
                    document.getElementById('series-rating-value').textContent = ((existing + rating) / 2).toFixed(1);
                } else {
                    document.getElementById('series-rating-value').textContent = rating.toFixed(1);
                }
            });
        });
    </script>
</x-layouts.app>
