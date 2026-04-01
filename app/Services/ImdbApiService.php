<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ImdbApiService
{
    public function search(string $query, int $limit = 24): array
    {
        $response = $this->client()->get('/search/titles', [
            'query' => $query,
            'limit' => $limit,
        ])->throw()->json();

        return Arr::get($response, 'titles', Arr::get($response, 'results', []));
    }

    public function trending(string $timeWindow = 'WEEK', int $limit = 24): array
    {
        $response = $this->client()->get('/titles/trending', [
            'timeWindow' => $timeWindow,
            'limit' => $limit,
        ])->throw()->json();

        return Arr::get($response, 'titles', Arr::get($response, 'results', []));
    }

    public function findById(string $id): array
    {
        return $this->client()->get("/titles/{$id}")->throw()->json();
    }

    public function similar(string $id, int $limit = 12): array
    {
        $response = $this->client()->get("/titles/{$id}/more-like-this", [
            'limit' => $limit,
        ])->throw()->json();

        return Arr::get($response, 'titles', Arr::get($response, 'results', []));
    }

    public function seasons(string $seriesId): array
    {
        $response = $this->safeGet("/titles/{$seriesId}/seasons");

        $seasons = Arr::get($response, 'seasons', Arr::get($response, 'results', []));

        if ($seasons !== []) {
            return $seasons;
        }

        $details = $this->safeGet("/titles/{$seriesId}");

        return Arr::get($details, 'seasons', []);
    }

    public function episodes(string $seriesId, ?int $season = null): array
    {
        $response = $this->safeGet("/titles/{$seriesId}/episodes", array_filter([
            'season' => $season,
        ]));

        $episodes = Arr::get($response, 'episodes', Arr::get($response, 'results', []));

        if ($episodes === [] && $season !== null) {
            $fallback = $this->safeGet("/titles/{$seriesId}/seasons/{$season}");
            $episodes = Arr::get($fallback, 'episodes', []);
        }

        return $episodes;
    }

    public function credits(string $titleId): array
    {
        $response = $this->safeGet("/titles/{$titleId}/credits");

        return Arr::get($response, 'credits', Arr::get($response, 'results', []));
    }

    public function seasonPoster(string $seriesId, int $season): ?string
    {
        $response = $this->safeGet("/titles/{$seriesId}/seasons/{$season}");

        return Arr::get($response, 'primaryImage');
    }


    public function safePerson(string $id): array
    {
        $response = $this->safeGet("/names/{$id}");

        if ($response !== []) {
            return $response;
        }

        return $this->safeGet("/people/{$id}");
    }
    protected function safeGet(string $path, array $query = []): array
    {
        $response = $this->client()->get($path, $query);

        if (! $response->successful()) {
            return [];
        }

        return $response->json();
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.imdb.base_url'), '/'))
            ->acceptJson()
            ->timeout(15)
            ->when(config('services.imdb.api_key'), function (PendingRequest $request, string $apiKey): PendingRequest {
                return $request->withToken($apiKey);
            });
    }
}
