<x-layouts.app :title="($series['primaryTitle'] ?? 'Series').' - Season '.$seasonNumber">
    <div style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start">
        <img src="{{ $poster ?? 'https://placehold.co/300x450?text=Season' }}" alt="Season poster" style="width:100%;border-radius:8px">
        <div>
            <h1>{{ $series['primaryTitle'] ?? 'Series' }} · Season {{ $seasonNumber }}</h1>
            <p>{{ $series['description'] ?? 'No season synopsis available.' }}</p>
            <button type="button" id="mark-season-watched">Mark whole season watched</button>
        </div>
    </div>

    <h2 style="margin-top:20px">Episodes</h2>
    <table style="width:100%;border-collapse:collapse;background:#1a1c23;border-radius:8px;overflow:hidden">
        <thead>
            <tr style="text-align:left;background:#20232d">
                <th style="padding:10px">#</th>
                <th>Episode</th>
                <th>Air date</th>
                <th>Runtime</th>
                <th>Rating</th>
                <th>Watched</th>
            </tr>
        </thead>
        <tbody>
        @foreach($episodes as $episode)
            @php($episodeId = $episode['id'] ?? $episode['tconst'] ?? null)
            @continue(!$episodeId)
            <tr class="episode-row" data-episode-id="{{ $episodeId }}" style="border-top:1px solid #2c2f3a">
                <td style="padding:10px">{{ $episode['episodeNumber'] ?? '—' }}</td>
                <td>
                    <a href="{{ route('series.episode', ['id' => $series['id'], 'season' => $seasonNumber, 'episodeId' => $episodeId]) }}">
                        <img src="{{ $episode['primaryImage'] ?? 'https://placehold.co/120x68?text=EP' }}" alt="thumb" style="width:80px;height:45px;object-fit:cover;border-radius:4px;vertical-align:middle;margin-right:8px">
                        {{ $episode['primaryTitle'] ?? 'Episode' }}
                    </a>
                </td>
                <td>{{ $episode['releaseDate'] ?? 'TBA' }}</td>
                <td>{{ $episode['runtimeMinutes'] ?? '—' }} min</td>
                <td><button type="button" class="rate-ep" data-base="{{ $episode['averageRating'] ?? 0 }}">⭐ <span>{{ $episode['averageRating'] ?? 'N/A' }}</span></button></td>
                <td><input type="checkbox" class="episode-watched"></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script>
        const key = 'season-watched-{{ $series['id'] ?? 'series' }}-{{ $seasonNumber }}';
        const checkboxes = document.querySelectorAll('.episode-watched');
        const saved = JSON.parse(localStorage.getItem(key) || '[]');
        checkboxes.forEach((checkbox, index) => checkbox.checked = saved.includes(index));

        checkboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', () => {
                const watched = [...checkboxes].map((cb, i) => cb.checked ? i : null).filter((i) => i !== null);
                localStorage.setItem(key, JSON.stringify(watched));
            });
        });

        document.getElementById('mark-season-watched').addEventListener('click', () => {
            checkboxes.forEach((cb) => cb.checked = true);
            localStorage.setItem(key, JSON.stringify([...checkboxes].map((_, i) => i)));
        });

        document.querySelectorAll('.rate-ep').forEach((button) => {
            button.addEventListener('click', () => {
                const current = Number(button.querySelector('span').textContent);
                const simulatedVote = Math.max(1, Math.min(10, Math.round((current || 7) + 1)));
                button.querySelector('span').textContent = simulatedVote.toFixed(1);
            });
        });
    </script>
</x-layouts.app>
