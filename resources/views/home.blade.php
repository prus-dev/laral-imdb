<x-layouts.app :title="'LaraIMDb | Discovery Hub'">
    @if($hero)
        <section class="hero">
            <img src="{{ $hero['primaryImage'] ?? 'https://placehold.co/1100x450?text=Featured+Title' }}" alt="{{ $hero['primaryTitle'] ?? 'Featured title' }}">
            <div>
                <p class="eyebrow">Featured right now</p>
                <h1>{{ $hero['primaryTitle'] ?? 'Featured title' }}</h1>
                <p class="muted">⭐ {{ $hero['averageRating'] ?? 'N/A' }} · {{ $hero['startYear'] ?? '—' }}</p>
                <a class="btn" href="{{ route('movies.show', $hero['id'] ?? $hero['tconst']) }}">Open title page</a>
            </div>
        </section>
    @endif

    @foreach($sections as $section)
        <section class="section-block">
            <div class="section-head">
                <h2>{{ $section['label'] }}</h2>
                <a href="{{ route('movies.browse', array_merge(['sort' => 'popularity'], $section['filters'])) }}">See all</a>
            </div>
            <div class="carousel">
                @foreach($section['items'] as $movie)
                    @php($id = $movie['id'] ?? $movie['tconst'] ?? null)
                    @continue(!$id)
                    <article class="card">
                        <a href="{{ route('movies.show', $id) }}">
                            <img src="{{ $movie['primaryImage'] ?? $movie['image']['url'] ?? 'https://placehold.co/300x450?text=No+Image' }}" alt="{{ $movie['primaryTitle'] ?? 'Poster' }}">
                            <h4>{{ $movie['primaryTitle'] ?? 'Untitled' }}</h4>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach

    <section class="section-block">
        <div class="section-head"><h2>Popular celebrities</h2></div>
        <div class="carousel">
            @foreach($people as $person)
                <article class="card">
                    <a href="{{ route('people.show', ['id' => $person['id'], 'name' => $person['name']]) }}">
                        <img src="https://placehold.co/300x300?text={{ urlencode($person['name']) }}" alt="{{ $person['name'] }} headshot">
                        <h4>{{ $person['name'] }}</h4>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section-block">
        <div class="section-head"><h2>Latest trailers</h2></div>
        <div class="carousel trailers">
            @foreach($trailers as $trailer)
                <article class="card">
                    <a href="{{ route('trailers.show', ['id' => $trailer['id'], 'title' => $trailer['title']]) }}">
                        <img src="{{ $trailer['poster'] }}" alt="{{ $trailer['title'] }}">
                        <h4>▶ {{ $trailer['title'] }}</h4>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section-block">
        <div class="section-head"><h2>Collections</h2></div>
        <div class="grid quick-grid">
            @foreach($featuredCollections as $collection)
                <a class="quick-pill" href="{{ route('movies.browse', $collection['filters']) }}">{{ $collection['title'] }}</a>
            @endforeach
        </div>
    </section>

    @foreach($quickLinks as $type => $values)
        <section class="section-block">
            <div class="section-head"><h2>Browse by {{ $type }}</h2></div>
            <div class="grid quick-grid">
                @foreach($values as $value)
                    <a class="quick-pill" href="{{ route('movies.browse', [$type => $value]) }}">{{ $value }}</a>
                @endforeach
            </div>
        </section>
    @endforeach
</x-layouts.app>
