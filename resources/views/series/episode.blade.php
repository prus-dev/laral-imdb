<x-layouts.app :title="($episode['primaryTitle'] ?? 'Episode').' | '.$series['primaryTitle']">
    <p><a href="{{ route('series.show', $series['id']) }}">{{ $series['primaryTitle'] ?? 'Series' }}</a> / <a href="{{ route('series.season', ['id' => $series['id'], 'season' => $seasonNumber]) }}">Season {{ $seasonNumber }}</a></p>
    <h1>{{ $episode['primaryTitle'] ?? 'Untitled Episode' }}</h1>
    <p class="muted">S{{ $seasonNumber }}E{{ $episode['episodeNumber'] ?? '—' }} · {{ $episode['releaseDate'] ?? 'TBA' }} · {{ $episode['runtimeMinutes'] ?? '—' }} min · ⭐ {{ $episode['averageRating'] ?? 'N/A' }}</p>

    <p>{{ $episode['description'] ?? $episode['plot'] ?? 'No plot summary available.' }}</p>

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin:12px 0">
        @foreach(($episode['images'] ?? [$episode['primaryImage'] ?? null]) as $img)
            @continue(!$img)
            <img src="{{ is_array($img) ? ($img['url'] ?? null) : $img }}" alt="still" style="width:190px;height:107px;object-fit:cover;border-radius:6px">
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="card">
            <h3>Guest stars & credits</h3>
            @forelse($credits as $credit)
                @php($personId = $credit['person']['id'] ?? $credit['id'] ?? null)
                <p>
                    @if($personId)
                        <a href="{{ route('people.show', $personId) }}">{{ $credit['person']['name'] ?? $credit['name'] ?? 'Unknown' }}</a>
                    @else
                        {{ $credit['person']['name'] ?? $credit['name'] ?? 'Unknown' }}
                    @endif
                    <span class="muted">— {{ $credit['category'] ?? $credit['job'] ?? 'Cast/Crew' }}</span>
                </p>
            @empty
                <p class="muted">Credits unavailable.</p>
            @endforelse
        </div>

        <div class="card">
            <h3>User reviews</h3>
            <p class="muted">Discussion entry points:</p>
            <ul>
                <li><a href="#">Write a review</a></li>
                <li><a href="#">Join episode discussion</a></li>
                <li><a href="#">Add trivia</a></li>
                <li><a href="#">Add quote</a></li>
                <li><a href="#">Add connection</a></li>
            </ul>
        </div>
    </div>
</x-layouts.app>
