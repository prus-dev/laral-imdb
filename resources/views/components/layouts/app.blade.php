<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'LaraIMDb' }}</title>
    <style>
        body{font-family:Arial,sans-serif;margin:0;background:#0f1014;color:#fff}
        a{color:#f5c518;text-decoration:none}
        .container{max-width:1100px;margin:0 auto;padding:20px}
        .header{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:15px 0}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px}
        .card{background:#1a1c23;padding:10px;border-radius:8px}
        .card img{width:100%;height:260px;object-fit:cover;border-radius:6px}
        .carousel{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:14px}
        .trailers .card img{height:140px}
        .section-block{margin-bottom:26px}
        .section-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
        .hero{display:grid;grid-template-columns:2fr 1fr;gap:20px;background:#1a1c23;padding:12px;border-radius:10px;margin-bottom:28px}
        .hero img{width:100%;height:360px;object-fit:cover;border-radius:8px}
        .eyebrow{text-transform:uppercase;letter-spacing:.07em;color:#f5c518;font-size:.8rem;margin-bottom:6px}
        .btn{display:inline-block;background:#f5c518;color:#111;padding:10px 14px;border-radius:6px;font-weight:700}
        .quick-grid{grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px}
        .quick-pill{display:block;background:#1a1c23;padding:10px;border-radius:999px;text-align:center;color:#fff;border:1px solid #2a2c36}
        .muted{color:#b8b8b8;font-size:.9rem}
        input,button{padding:10px;border-radius:6px;border:1px solid #333}
        button{background:#f5c518;color:#111;font-weight:700;cursor:pointer}
        .flash{background:#273820;color:#b8ffaf;padding:10px;border-radius:6px;margin-bottom:10px}
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <h2><a href="{{ route('home') }}">LaraIMDb</a></h2>
        <nav>
            <a href="{{ route('movies.search') }}">Search</a> |
            <a href="{{ route('watchlist.index') }}">Watchlist</a>
        </nav>
    </header>

    @if(session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    {{ $slot }}
</div>
</body>
</html>
