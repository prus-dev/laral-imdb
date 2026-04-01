<x-layouts.app :title="($person['name'] ?? 'Person').' | LaraIMDb'">
    <h1>{{ $person['name'] ?? 'Person profile' }}</h1>
    <p>{{ $person['bio'] ?? 'No biography available.' }}</p>
</x-layouts.app>
