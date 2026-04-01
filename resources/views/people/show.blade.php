<x-layouts.app :title="$name.' | LaraIMDb'">
    <h1>{{ $name }}</h1>
    <p class="muted">Person page · ID {{ $id }}</p>
    <img src="https://placehold.co/320x320?text={{ urlencode($name) }}" alt="{{ $name }} headshot" style="border-radius:10px">
</x-layouts.app>
