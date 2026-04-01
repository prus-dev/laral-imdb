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

    public function reviews(string $id, int $limit = 100): array
    {
        $response = $this->client()->get("/titles/{$id}/reviews", [
            'limit' => $limit,
        ])->throw()->json();

        return Arr::get($response, 'reviews', Arr::get($response, 'results', []));
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
