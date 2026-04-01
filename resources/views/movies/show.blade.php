<x-layouts.app :title="($movie['primaryTitle'] ?? 'Title').' | LaraIMDb'">
    <div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">
        <img src="{{ $movie['primaryImage'] ?? 'https://placehold.co/400x600?text=No+Image' }}" alt="Poster" style="width:100%;border-radius:8px">
        <div>
            <h1>{{ $movie['primaryTitle'] ?? 'Untitled' }}</h1>
            <p class="muted">{{ $movie['startYear'] ?? '—' }} · {{ $movie['runtimeMinutes'] ?? '—' }} min · ⭐ {{ $movie['averageRating'] ?? 'N/A' }}</p>
            <p>{{ $movie['description'] ?? $movie['plot'] ?? 'No plot available.' }}</p>

            <section style="margin:18px 0;padding:14px;border:1px solid #2b2f39;border-radius:10px;background:#141822">
                <h3 style="margin:0 0 8px">Rate this {{ $contextType }}</h3>
                <p class="muted" id="rating-label">Pick a score from 1 to 10.</p>
                <div id="score-selector" style="display:flex;flex-wrap:wrap;gap:6px;margin:10px 0" data-current="{{ $userRating?->score ?? '' }}">
                    @for($score = 1; $score <= 10; $score++)
                        <button type="button" class="score-btn" data-score="{{ $score }}" style="min-width:40px">{{ $score }}</button>
                    @endfor
                </div>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                    <button type="button" id="remove-rating" style="background:#2f3645;color:#fff">Remove rating</button>
                    <span class="muted">Your rating: <strong id="user-rating-value">{{ $userRating?->score ?? 'Not rated' }}</strong></span>
                    <span class="muted">Community: <strong id="aggregate-value">{{ $aggregate->average_score ?? '0.0' }}</strong> (<span id="aggregate-count">{{ $aggregate->ratings_count ?? 0 }}</span>)</span>
                </div>
                <div style="margin-top:10px" class="muted">Profile stats: Rated <strong id="profile-ratings">{{ $profile->ratings_count ?? 0 }}</strong> · Avg <strong id="profile-average">{{ $profile->average_score ?? '0.0' }}</strong> · Watched <strong id="profile-watched">{{ $profile->watched_count ?? 0 }}</strong></div>
                <div id="rating-toast" class="flash" style="display:none;margin-top:10px"></div>

                <div id="review-modal" style="display:none;margin-top:10px;padding:12px;border:1px solid #3f4e31;background:#1f2b1f;border-radius:8px">
                    <strong>Nice! Want to add a review?</strong>
                    <p class="muted" style="margin:6px 0 0">Optional: share what you liked/disliked while it's fresh.</p>
                </div>

                <h4 style="margin:14px 0 8px">Rating history</h4>
                <ul id="rating-history" class="muted" style="margin:0;padding-left:18px">
                    @forelse($history as $event)
                        <li>{{ $event->created_at?->format('Y-m-d H:i') }} · {{ $event->context_type }} · {{ $event->action }} {{ $event->new_score ? ('→ '.$event->new_score) : '' }}</li>
                    @empty
                        <li>No rating events yet.</li>
                    @endforelse
                </ul>
            </section>

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

    <script>
        const contextType = @json($contextType);
        const imdbId = @json($movie['id'] ?? '');
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const scoreSelector = document.getElementById('score-selector');
        const label = document.getElementById('rating-label');
        const toast = document.getElementById('rating-toast');
        const modal = document.getElementById('review-modal');

        const labels = {
            1: '1 — Awful', 2: '2 — Very bad', 3: '3 — Bad', 4: '4 — Meh', 5: '5 — Average',
            6: '6 — Fine', 7: '7 — Good', 8: '8 — Very good', 9: '9 — Excellent', 10: '10 — Masterpiece',
        };

        function paintButtons(active) {
            document.querySelectorAll('.score-btn').forEach((btn) => {
                const isActive = Number(btn.dataset.score) === Number(active);
                btn.style.background = isActive ? '#f5c518' : '#2f3645';
                btn.style.color = isActive ? '#111' : '#fff';
            });
        }

        async function sendRating(method, payload = {}) {
            const response = await fetch(method === 'DELETE' ? '{{ route('ratings.destroy') }}' : '{{ route('ratings.store') }}', {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ imdb_id: imdbId, context_type: contextType, ...payload }),
            });

            if (!response.ok) return;
            const data = await response.json();
            document.getElementById('aggregate-value').textContent = data.aggregate.average_score.toFixed(1);
            document.getElementById('aggregate-count').textContent = data.aggregate.ratings_count;
            document.getElementById('profile-ratings').textContent = data.profile.ratings_count;
            document.getElementById('profile-average').textContent = data.profile.average_score.toFixed(1);
            document.getElementById('profile-watched').textContent = data.profile.watched_count;
            toast.textContent = data.toast.message;
            toast.style.display = 'block';
            modal.style.display = data.show_review_prompt ? 'block' : 'none';

            const historyList = document.getElementById('rating-history');
            historyList.innerHTML = data.history.length
                ? data.history.map((event) => `<li>${new Date(event.created_at).toLocaleString()} · ${event.context_type} · ${event.action} ${event.new_score ? '→ '+event.new_score : ''}</li>`).join('')
                : '<li>No rating events yet.</li>';
        }

        document.querySelectorAll('.score-btn').forEach((btn) => {
            btn.addEventListener('mouseenter', () => {
                const score = Number(btn.dataset.score);
                label.textContent = labels[score] ?? 'Pick a score from 1 to 10.';
                paintButtons(score);
            });

            btn.addEventListener('click', async () => {
                const score = Number(btn.dataset.score);
                await sendRating('POST', { score });
                document.getElementById('user-rating-value').textContent = score;
                scoreSelector.dataset.current = score;
                paintButtons(score);
            });
        });

        scoreSelector.addEventListener('mouseleave', () => {
            const current = Number(scoreSelector.dataset.current || 0);
            label.textContent = current ? labels[current] : 'Pick a score from 1 to 10.';
            paintButtons(current);
        });

        document.getElementById('remove-rating').addEventListener('click', async () => {
            await sendRating('DELETE');
            scoreSelector.dataset.current = '';
            document.getElementById('user-rating-value').textContent = 'Not rated';
            label.textContent = 'Pick a score from 1 to 10.';
            paintButtons(0);
        });

        paintButtons(Number(scoreSelector.dataset.current || 0));
    </script>
</x-layouts.app>
