<x-layouts.app :title="$title.' | LaraIMDb'">
    <h1>{{ $title }}</h1>
    <p class="muted">Trailer page · ID {{ $id }}</p>
    <div class="card" style="max-width:760px">
        <img src="https://placehold.co/760x428?text=Trailer+Player" alt="Trailer player" style="height:auto">
        <p class="muted" style="margin-top:8px">Trailer modal/page placeholder.</p>
    </div>
</x-layouts.app>
