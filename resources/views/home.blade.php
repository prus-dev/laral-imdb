<x-layouts.app :title="'LaraIMDb | Discovery Hub'">
    @php
        $heroId = $hero['id'] ?? $hero['tconst'] ?? null;
        $heroImage = $hero['primaryImage'] ?? $hero['image']['url'] ?? 'https://placehold.co/1200x500?text=Featured+Title';
        $heroTitle = $hero['primaryTitle'] ?? 'Featured title';
    @endphp

    <section class="hero" style="background-image:url('{{ $heroImage }}')">
        <div class="hero__overlay">
            <p class="muted">Featured now</p>
            <h1>{{ $heroTitle }}</h1>
            <p>Explore what everyone is talking about right now.</p>
            @if($heroId)
                <a class="btn" href="{{ route('movies.show', $heroId) }}">Open title</a>
            @endif
        </div>
    </section>

    @php
        $sectionLabels = [
            'trendingNow' => 'Trending now',
            'topRatedMovies' => 'Top rated movies',
            'topRatedTvShows' => 'Top rated TV shows',
            'mostAnticipated' => 'Most anticipated',
            'inTheaters' => 'In theaters',
            'streamingNow' => 'Streaming now',
            'recentlyAdded' => 'Recently added titles',
            'popularCelebrities' => 'Popular celebrities',
            'latestTrailers' => 'Latest trailers',
            'latestReviews' => 'Latest reviews',
            'featuredLists' => 'Featured lists',
            'editorialPicks' => 'Editorial picks',
            'awardSeasonSpotlight' => 'Award season spotlight',
        ];
    @endphp

    @foreach($sectionLabels as $key => $label)
        @php
            $items = $collections[$key] ?? [];
            $slug = \Illuminate\Support\Str::slug($label);
        @endphp
        <section class="section">
            <div class="section__header">
                <h2>{{ $label }}</h2>
                <a href="{{ route('browse.collection', $slug) }}">See all</a>
            </div>
            <div class="carousel">
                @foreach($items as $item)
                    @php
                        $id = $item['id'] ?? $item['tconst'] ?? null;
                        $image = $item['primaryImage'] ?? $item['image']['url'] ?? 'https://placehold.co/240x360?text=No+Image';
                        $title = $item['primaryTitle'] ?? 'Untitled';
                    @endphp
                    @continue(!$id)
                    <article class="card card--tight">
                        @if($key === 'popularCelebrities')
                            <a href="{{ route('people.show', $id) }}">
                        @elseif($key === 'latestTrailers')
                            <a href="{{ route('trailers.show', $id) }}">
                        @elseif(in_array($key, ['featuredLists', 'latestReviews', 'editorialPicks'], true))
                            <a href="{{ route('browse.collection', $slug) }}">
                        @else
                            <a href="{{ route('movies.show', $id) }}">
                        @endif
                            <img src="{{ $image }}" alt="{{ $title }}">
                            <h4>{{ $title }}</h4>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach

    <section class="section">
        <h2>Genre quick links</h2>
        <div class="chips">
            @foreach($taxonomyLinks['genre'] as $value)
                <a class="chip" href="{{ route('browse.discover', ['genre' => $value]) }}">{{ $value }}</a>
            @endforeach
        </div>
    </section>

    @foreach(['mood' => 'Browse by mood', 'language' => 'Browse by language', 'country' => 'Browse by country', 'year' => 'Browse by release year', 'network' => 'Browse by network / streaming platform'] as $key => $title)
        <section class="section">
            <h2>{{ $title }}</h2>
            <div class="chips">
                @foreach($taxonomyLinks[$key] as $value)
                    <a class="chip" href="{{ route('browse.discover', [$key => $value]) }}">{{ $value }}</a>
                @endforeach
            </div>
        </section>
    @endforeach
</x-layouts.app>
