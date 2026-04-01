# LaraIMDb (Laravel 12 IMDb Clone)

A Laravel 12-style IMDb clone using **imdbapi.dev** for live movie/title data.

## Features

- Trending titles homepage
- Search titles by keyword
- Title details page with metadata and plot
- "More Like This" recommendations
- Local watchlist with add/remove actions
- SQLite persistence for watchlist

## Why this repo is scaffolded (and not fully installed)

The current environment blocks direct package downloads from Packagist/GitHub, so dependencies cannot be installed here. Code is structured for Laravel 12; to run locally, install dependencies on a machine with package access.

## Local setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

Then open `http://127.0.0.1:8000`.

## API setup

Get API access from `https://imdbapi.dev/` and set:

```env
IMDB_API_KEY=your_token_here
IMDB_API_BASE_URL=https://api.imdbapi.dev
```

## Main routes

- `/` – trending
- `/search?q=...` – search
- `/title/{id}` – detail page
- `/watchlist` – saved watchlist
