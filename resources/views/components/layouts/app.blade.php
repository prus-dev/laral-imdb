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
        .muted{color:#b8b8b8;font-size:.9rem}
        input,button{padding:10px;border-radius:6px;border:1px solid #333}
        button{background:#f5c518;color:#111;font-weight:700;cursor:pointer}
        .flash{background:#273820;color:#b8ffaf;padding:10px;border-radius:6px;margin-bottom:10px}

        .hero{background-size:cover;background-position:center;min-height:280px;border-radius:12px;overflow:hidden;margin-bottom:20px}
        .hero__overlay{background:linear-gradient(90deg,rgba(0,0,0,.8),rgba(0,0,0,.3));padding:28px;height:100%}
        .btn{display:inline-block;background:#f5c518;color:#111;padding:10px 14px;border-radius:8px;font-weight:700}
        .section{margin:26px 0}
        .section__header{display:flex;justify-content:space-between;align-items:center;gap:12px}
        .carousel{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px}
        .card--tight img{height:210px}
        .chips{display:flex;flex-wrap:wrap;gap:10px}
        .chip{display:inline-block;padding:8px 12px;border-radius:999px;background:#1a1c23;color:#fff;border:1px solid #2f3340}
        .filterbar{display:flex;gap:10px;align-items:center;margin-bottom:16px}
        select{padding:10px;border-radius:6px;border:1px solid #333;background:#0f1014;color:#fff}
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
