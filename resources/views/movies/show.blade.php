<x-layouts.app :title="($movie['primaryTitle'] ?? 'Title').' | LaraIMDb'">
    @php
        $title = $movie['primaryTitle'] ?? $movie['titleText']['text'] ?? 'Untitled';
        $originalTitle = $movie['originalTitle'] ?? $movie['originalTitleText']['text'] ?? 'Not available';
        $alternateTitles = $movie['akas'] ?? $movie['alternateTitles'] ?? [];
        $poster = $movie['primaryImage'] ?? $movie['image']['url'] ?? 'https://placehold.co/400x600?text=No+Image';
        $backdrop = $movie['primaryImage'] ?? $movie['coverImage']['url'] ?? 'https://placehold.co/1200x675?text=Backdrop';
        $year = $movie['startYear'] ?? $movie['releaseYear']['year'] ?? '—';
        $releaseDate = $movie['releaseDate'] ?? ($movie['releaseDate']['day'] ?? null ? (($movie['releaseDate']['year'] ?? '—').'-'.($movie['releaseDate']['month'] ?? '—').'-'.($movie['releaseDate']['day'] ?? '—')) : null);
        $runtimeMinutes = $movie['runtimeMinutes'] ?? null;
        $genres = $movie['genres'] ?? $movie['genreNames'] ?? [];
        $subgenres = $movie['subgenres'] ?? [];
        $countries = $movie['countriesOfOrigin'] ?? $movie['originCountries'] ?? [];
        $languages = $movie['spokenLanguages'] ?? $movie['languages'] ?? [];
        $tagline = $movie['tagline'] ?? null;
        $shortPlot = $movie['description'] ?? $movie['plot'] ?? null;
        $fullPlot = $movie['plotText']['plainText'] ?? $movie['plot'] ?? $shortPlot;
        $rating = $movie['averageRating'] ?? 'N/A';
        $votes = $movie['numVotes'] ?? $movie['voteCount'] ?? null;
        $popularity = $movie['popularity'] ?? null;
        $criticsScore = $movie['metacritic'] ?? $movie['criticsScore'] ?? null;
        $rank = $movie['rank'] ?? null;
        $awardsSummary = $movie['awardsSummary'] ?? null;
        $streaming = $movie['watchOptions'] ?? $movie['streamingAvailability'] ?? [];
        $franchise = $movie['franchise'] ?? $movie['series'] ?? null;
        $isTv = ($movie['titleType'] ?? '') === 'tvSeries' || ($movie['isSeries'] ?? false);

        $cast = $movie['cast'] ?? [];
        $crew = $movie['directors'] ?? $movie['crew'] ?? [];
        $techSpecs = $movie['technicalSpecs'] ?? [];
        $awards = $movie['awards'] ?? [];
        $trivia = $movie['trivia'] ?? [];
        $goofs = $movie['goofs'] ?? [];
        $soundtrack = $movie['soundtrack'] ?? [];
        $quotes = $movie['quotes'] ?? [];
        $parentsGuide = $movie['parentsGuide'] ?? null;
    @endphp

    <style>
        .hero{position:relative;border-radius:12px;overflow:hidden;background:#181b24;margin-bottom:20px}
        .hero::before{content:'';position:absolute;inset:0;background:url('{{ $backdrop }}') center/cover no-repeat;opacity:.3}
        .hero-inner{position:relative;z-index:1;display:grid;grid-template-columns:220px 1fr;gap:20px;padding:24px}
        .poster{width:100%;border-radius:10px;box-shadow:0 12px 30px rgba(0,0,0,.4)}
        .meta-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;margin-top:12px}
        .meta-item{background:#1b1f2a;padding:10px;border-radius:8px}
        .actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
        .actions button,.actions a{background:#252a37;color:#fff;border:1px solid #30374a;padding:8px 10px;border-radius:8px;font-size:.9rem}
        .actions .primary{background:#f5c518;color:#111;border:0;font-weight:700}
        .section-nav{display:flex;flex-wrap:wrap;gap:8px;margin:18px 0}
        .section-nav a{padding:6px 10px;background:#1a1d27;border-radius:999px;color:#ddd;font-size:.85rem}
        .section{background:#171a22;border:1px solid #272d3b;border-radius:10px;padding:14px;margin-bottom:14px}
        .section h3{margin:0 0 8px 0}
        .mini-list{margin:0;padding-left:18px}
    </style>

    <section class="hero">
        <div class="hero-inner">
            <img class="poster" src="{{ $poster }}" alt="{{ $title }} poster">
            <div>
                <h1>{{ $title }}</h1>
                <p class="muted">{{ $year }} · {{ $runtimeMinutes ? $runtimeMinutes.' min' : 'Runtime unavailable' }} · ⭐ {{ $rating }}</p>
                <p>{{ $shortPlot ?? 'No plot summary available.' }}</p>
                @if($tagline)
                    <p><em>“{{ $tagline }}”</em></p>
                @endif

                <div class="actions">
                    <button class="primary" type="button">Rate title</button>
                    <form method="POST" action="{{ route('watchlist.store') }}" style="display:inline">
                        @csrf
                        <input type="hidden" name="imdb_id" value="{{ $movie['id'] ?? '' }}">
                        <input type="hidden" name="title" value="{{ $title }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="poster_url" value="{{ $poster }}">
                        <input type="hidden" name="rating" value="{{ is_numeric($rating) ? $rating : '' }}">
                        <input type="hidden" name="runtime_seconds" value="{{ $runtimeMinutes ? (int)$runtimeMinutes * 60 : '' }}">
                        <input type="hidden" name="type" value="{{ $movie['titleType'] ?? '' }}">
                        <button type="submit">Add to watchlist</button>
                    </form>
                    <button type="button">Mark as watched</button>
                    <button type="button">Add to custom list</button>
                    <button type="button">Write review</button>
                    <button type="button">Share</button>
                    <button type="button">Report issue</button>
                    <button type="button">Suggest edit</button>
                    <button type="button">Watch trailer</button>
                    <button type="button">Open photo gallery</button>
                    <button type="button">Expand cast</button>
                    <button type="button">Expand crew</button>
                    <button type="button">Expand technical details</button>
                    <button type="button">Expand awards</button>
                    <button type="button">Expand trivia</button>
                    <button type="button">Expand goofs</button>
                    <button type="button">Expand soundtrack</button>
                    <button type="button">Expand quotes</button>
                    <button type="button">Expand parents guide</button>
                </div>
            </div>
        </div>
    </section>

    <div class="meta-grid">
        <div class="meta-item"><strong>Original title:</strong> {{ $originalTitle }}</div>
        <div class="meta-item"><strong>Alternate titles:</strong> {{ collect($alternateTitles)->take(4)->implode(', ') ?: 'Not available' }}</div>
        <div class="meta-item"><strong>Full release date:</strong> {{ $releaseDate ?? 'Not available' }}</div>
        <div class="meta-item"><strong>Genres:</strong> {{ collect($genres)->flatten()->implode(', ') ?: 'Not available' }}</div>
        <div class="meta-item"><strong>Subgenres:</strong> {{ collect($subgenres)->implode(', ') ?: 'Not available' }}</div>
        <div class="meta-item"><strong>Country of origin:</strong> {{ collect($countries)->flatten()->implode(', ') ?: 'Not available' }}</div>
        <div class="meta-item"><strong>Languages:</strong> {{ collect($languages)->flatten()->implode(', ') ?: 'Not available' }}</div>
        <div class="meta-item"><strong>Content rating:</strong> {{ $movie['certificate'] ?? $movie['rating'] ?? 'Not available' }}</div>
        <div class="meta-item"><strong>Production status:</strong> {{ $movie['productionStatus'] ?? 'Not available' }}</div>
        <div class="meta-item"><strong>Vote count:</strong> {{ $votes ?? 'Not available' }}</div>
        <div class="meta-item"><strong>Popularity score:</strong> {{ $popularity ?? 'Not available' }}</div>
        <div class="meta-item"><strong>Critics score:</strong> {{ $criticsScore ?? 'Optional / unavailable' }}</div>
        <div class="meta-item"><strong>Rank within site:</strong> {{ $rank ?? 'Not available' }}</div>
        <div class="meta-item"><strong>Awards summary:</strong> {{ $awardsSummary ?? 'Not available' }}</div>
        <div class="meta-item"><strong>Streaming availability:</strong> {{ collect($streaming)->flatten()->implode(', ') ?: 'Not available' }}</div>
        <div class="meta-item"><strong>Franchise relation:</strong> {{ is_array($franchise) ? json_encode($franchise) : ($franchise ?? 'Not available') }}</div>
        <div class="meta-item"><strong>Average user rating:</strong> {{ $rating }}</div>
    </div>

    <nav class="section-nav">
        <a href="#overview">Overview</a><a href="#cast">Cast</a><a href="#crew">Crew</a><a href="#storyline">Storyline</a>
        <a href="#details">Details</a><a href="#box-office">Box office</a><a href="#technical-specs">Technical specs</a><a href="#release-info">Release info</a>
        <a href="#soundtrack">Soundtrack</a><a href="#trivia">Trivia</a><a href="#goofs">Goofs</a><a href="#quotes">Quotes</a>
        <a href="#user-reviews">User reviews</a><a href="#critic-reviews">Critic reviews</a><a href="#photos">Photos</a><a href="#videos">Videos / trailers</a>
        <a href="#similar-titles">Similar titles</a><a href="#people-liked">People who liked this also liked</a><a href="#awards-nominations">Awards & nominations</a>
        <a href="#related-news">Related news</a><a href="#where-to-watch">Where to watch</a><a href="#parents-guide">Parents guide</a>
        <a href="#faq">FAQ</a><a href="#connections">Connections / references</a><a href="#franchise-timeline">Franchise timeline</a>
        @if($isTv)
            <a href="#episode-guide">Episode guide</a><a href="#seasons">Seasons tab</a>
        @endif
        <a href="#recommendations">Recommendations</a>
    </nav>

    <section id="overview" class="section"><h3>Overview</h3><p>{{ $shortPlot ?? 'No overview available.' }}</p></section>
    <section id="cast" class="section"><h3>Cast</h3><ul class="mini-list">@forelse($cast as $person)<li>{{ is_array($person) ? ($person['name'] ?? json_encode($person)) : $person }}</li>@empty<li>Cast list not available.</li>@endforelse</ul></section>
    <section id="crew" class="section"><h3>Crew</h3><ul class="mini-list">@forelse($crew as $person)<li>{{ is_array($person) ? ($person['name'] ?? json_encode($person)) : $person }}</li>@empty<li>Crew list not available.</li>@endforelse</ul></section>
    <section id="storyline" class="section"><h3>Storyline</h3><p>{{ $fullPlot ?? 'No full plot summary available.' }}</p></section>
    <section id="details" class="section"><h3>Details</h3><p>Release year: {{ $year }} · Runtime: {{ $runtimeMinutes ? $runtimeMinutes.' min' : 'Not available' }} · Languages: {{ collect($languages)->flatten()->implode(', ') ?: 'Not available' }}</p></section>
    <section id="box-office" class="section"><h3>Box office</h3><p>{{ $movie['boxOffice'] ?? 'Box office data not available.' }}</p></section>
    <section id="technical-specs" class="section"><h3>Technical specs</h3><p>{{ !empty($techSpecs) ? json_encode($techSpecs) : 'Technical specs unavailable.' }}</p></section>
    <section id="release-info" class="section"><h3>Release info</h3><p>Full release date: {{ $releaseDate ?? 'Not available' }}</p></section>
    <section id="soundtrack" class="section"><h3>Soundtrack</h3><p>{{ !empty($soundtrack) ? json_encode($soundtrack) : 'Soundtrack unavailable.' }}</p></section>
    <section id="trivia" class="section"><h3>Trivia</h3><p>{{ !empty($trivia) ? json_encode($trivia) : 'Trivia unavailable.' }}</p></section>
    <section id="goofs" class="section"><h3>Goofs</h3><p>{{ !empty($goofs) ? json_encode($goofs) : 'Goofs unavailable.' }}</p></section>
    <section id="quotes" class="section"><h3>Quotes</h3><p>{{ !empty($quotes) ? json_encode($quotes) : 'Quotes unavailable.' }}</p></section>
    <section id="user-reviews" class="section"><h3>User reviews</h3><p>User reviews section coming soon.</p></section>
    <section id="critic-reviews" class="section"><h3>Critic reviews (optional)</h3><p>{{ $criticsScore ? 'Critics score: '.$criticsScore : 'Critic reviews unavailable.' }}</p></section>
    <section id="photos" class="section"><h3>Photos</h3><p>Open photo gallery to explore images.</p></section>
    <section id="videos" class="section"><h3>Videos / trailers</h3><p>Watch trailer action available in the main action block.</p></section>

    <section id="similar-titles" class="section">
        <h3>Similar titles</h3>
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
    </section>

    <section id="people-liked" class="section"><h3>People who liked this also liked</h3><p>Based on similar titles above.</p></section>
    <section id="awards-nominations" class="section"><h3>Awards & nominations</h3><p>{{ !empty($awards) ? json_encode($awards) : ($awardsSummary ?? 'No awards data available.') }}</p></section>
    <section id="related-news" class="section"><h3>Related news</h3><p>Related news integration coming soon.</p></section>
    <section id="where-to-watch" class="section"><h3>Where to watch</h3><p>{{ collect($streaming)->flatten()->implode(', ') ?: 'No provider data available.' }}</p></section>
    <section id="parents-guide" class="section"><h3>Parents guide</h3><p>{{ $parentsGuide ?? 'Parents guide unavailable.' }}</p></section>
    <section id="faq" class="section"><h3>FAQ</h3><p>FAQ section coming soon.</p></section>
    <section id="connections" class="section"><h3>Connections / references</h3><p>{{ $movie['connections'] ?? 'Connections data unavailable.' }}</p></section>
    <section id="franchise-timeline" class="section"><h3>Franchise timeline</h3><p>{{ is_array($franchise) ? json_encode($franchise) : ($franchise ?? 'No franchise data available.') }}</p></section>
    @if($isTv)
        <section id="episode-guide" class="section"><h3>Episode guide</h3><p>{{ $movie['episodeGuide'] ?? 'Episode guide unavailable.' }}</p></section>
        <section id="seasons" class="section"><h3>Seasons tab</h3><p>{{ $movie['seasons'] ?? 'Seasons information unavailable.' }}</p></section>
    @endif
    <section id="recommendations" class="section"><h3>Recommendations</h3><p>Use similar titles and trending pages to discover more.</p></section>
</x-layouts.app>
