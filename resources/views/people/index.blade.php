<x-layouts.app :title="'People Database | LaraIMDb'">
    <h1>People Database</h1>
    <p class="muted">Browse IMDb-style people profiles and profession types.</p>

    <h2>Person Types</h2>
    <div class="card" style="margin-bottom:20px">
        <ul style="margin:0;padding-left:20px;columns:2;gap:24px">
            @foreach($personTypes as $type)
                <li style="margin-bottom:6px">{{ $type }}</li>
            @endforeach
        </ul>
    </div>

    <h2>Search People</h2>
    <form method="GET" action="{{ route('people.index') }}" style="margin-bottom:20px;display:flex;gap:10px">
        <input type="text" name="q" value="{{ $query }}" placeholder="Search actors, directors, writers..." style="flex:1">
        <button type="submit">Search</button>
    </form>

    <div class="grid">
        @foreach($results as $person)
            @php($id = $person['id'] ?? $person['nconst'] ?? null)
            @continue(!$id)
            <div class="card">
                <a href="{{ route('people.show', $id) }}">
                    <img src="{{ $person['primaryImage'] ?? $person['image']['url'] ?? 'https://placehold.co/300x450?text=No+Photo' }}" alt="{{ $person['name'] ?? 'Person' }}">
                    <h4>{{ $person['name'] ?? 'Unknown person' }}</h4>
                </a>
                <p class="muted">{{ implode(', ', \Illuminate\Support\Arr::wrap($person['professions'] ?? $person['primaryProfession'] ?? [])) }}</p>
            </div>
        @endforeach
    </div>
</x-layouts.app>
