<x-layouts.app :title="'LaraIMDb | Person'">
    <h1>{{ $person['primaryTitle'] ?? 'Celebrity profile' }}</h1>
    <p class="muted">Public celebrity page for ID: {{ $id }}</p>
    <a href="{{ route('movies.search', ['q' => $person['primaryTitle'] ?? $id]) }}">See related titles</a>
</x-layouts.app>
