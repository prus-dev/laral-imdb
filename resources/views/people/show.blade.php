<x-layouts.app :title="($person['name'] ?? 'Person').' | LaraIMDb'">
    @php
        $knownFor = $person['knownFor'] ?? [];
        $professions = $person['professions'] ?? $person['primaryProfession'] ?? [];
        $awardsSummary = $person['awardsSummary'] ?? ($person['awards']['summary'] ?? null);
        $birthDate = $person['birthDate'] ?? null;
        $deathDate = $person['deathDate'] ?? null;
        $age = $person['age'] ?? null;
    @endphp

    <h1>Person Page</h1>
    <div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">
        <img src="{{ $person['primaryImage'] ?? $person['image']['url'] ?? 'https://placehold.co/400x600?text=No+Photo' }}" alt="{{ $person['name'] ?? 'Person' }}" style="width:100%;border-radius:8px">

        <div class="card">
            <h2 style="margin-top:0">{{ $person['name'] ?? 'Unknown person' }}</h2>
            <p class="muted">{{ implode(', ', \Illuminate\Support\Arr::wrap($professions)) }}</p>

            <p><strong>Alternate names:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['alternateNames'] ?? '—')) }}</p>
            <p><strong>Birth date:</strong> {{ $birthDate ?? '—' }}</p>
            <p><strong>Death date:</strong> {{ $deathDate ?? '—' }}</p>
            <p><strong>Age:</strong> {{ $age ?? '—' }}</p>
            <p><strong>Place of birth:</strong> {{ $person['birthPlace'] ?? '—' }}</p>
            <p><strong>Nationality:</strong> {{ $person['nationality'] ?? '—' }}</p>
            <p><strong>Known for:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($knownFor)) ?: '—' }}</p>
            <p><strong>Awards summary:</strong> {{ $awardsSummary ?? '—' }}</p>
            <p><strong>Short bio:</strong> {{ $person['shortBio'] ?? '—' }}</p>
            <p><strong>Full biography:</strong> {{ $person['biography'] ?? $person['bio'] ?? '—' }}</p>
            <p><strong>External links:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['externalLinks'] ?? '—')) }}</p>
            <p><strong>Social links (optional):</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['socialLinks'] ?? '—')) }}</p>
            <p><strong>Official website (optional):</strong> {{ $person['website'] ?? '—' }}</p>
        </div>
    </div>

    <h2 style="margin-top:24px">Filmography</h2>
    <form method="GET" action="{{ route('people.show', $person['id'] ?? request()->route('id')) }}" style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap">
        <label>
            Credit filter
            <select name="credit_filter">
                <option value="all" @selected($creditFilter === 'all')>All</option>
                @foreach($creditBuckets as $key => $bucket)
                    <option value="{{ $key }}" @selected($creditFilter === $key)>{{ $bucket['label'] }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Sort by
            <select name="sort">
                <option value="release_date" @selected($sort === 'release_date')>Release date</option>
                <option value="popularity" @selected($sort === 'popularity')>Popularity</option>
                <option value="rating" @selected($sort === 'rating')>Rating</option>
            </select>
        </label>
        <button type="submit">Apply</button>
    </form>

    @foreach($creditBuckets as $key => $bucket)
        @continue($creditFilter !== 'all' && $creditFilter !== $key)
        <h3>{{ $bucket['label'] }}</h3>
        <div class="grid" style="margin-bottom:16px">
            @forelse($bucket['items'] as $credit)
                @php($titleId = $credit['id'] ?? $credit['tconst'] ?? null)
                <div class="card">
                    @if($titleId)
                        <a href="{{ route('movies.show', $titleId) }}"><strong>{{ $credit['title'] ?? $credit['primaryTitle'] ?? 'Untitled' }}</strong></a>
                    @else
                        <strong>{{ $credit['title'] ?? $credit['primaryTitle'] ?? 'Untitled' }}</strong>
                    @endif
                    <p class="muted">{{ $credit['year'] ?? $credit['startYear'] ?? '—' }} · ⭐ {{ $credit['rating'] ?? $credit['averageRating'] ?? 'N/A' }}</p>
                    <p class="muted">{{ $credit['category'] ?? $credit['job'] ?? 'Credit' }}</p>
                </div>
            @empty
                <p class="muted">No credits available in this category.</p>
            @endforelse
        </div>
    @endforeach

    <h2>Other Sections</h2>
    <div class="card">
        <p><strong>Photos:</strong> Available on full profile integrations.</p>
        <p><strong>Videos / interviews:</strong> Available on full profile integrations.</p>
        <p><strong>Trivia:</strong> {{ $person['trivia'] ?? '—' }}</p>
        <p><strong>Quotes:</strong> {{ implode(' | ', \Illuminate\Support\Arr::wrap($person['quotes'] ?? '—')) }}</p>
        <p><strong>News mentions:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['newsMentions'] ?? '—')) }}</p>
        <p><strong>Related people:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['relatedPeople'] ?? '—')) }}</p>
        <p><strong>Collaborations:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['collaborations'] ?? '—')) }}</p>
        <p><strong>Awards:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['awards'] ?? '—')) }}</p>
        <p><strong>Lists that include this person:</strong> {{ implode(', ', \Illuminate\Support\Arr::wrap($person['lists'] ?? '—')) }}</p>
    </div>

    <h2 style="margin-top:24px">Actions</h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button type="button">Follow person</button>
        <button type="button">Add to favorites</button>
        <button type="button">Share person page</button>
        <button type="button">Suggest edit</button>
        <button type="button">Report issue</button>
    </div>
</x-layouts.app>
