<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function index(Request $request, ImdbApiService $imdb): View
    {
        $query = (string) $request->string('q');

        return view('people.index', [
            'query' => $query,
            'personTypes' => $this->personTypes(),
            'results' => $query !== '' ? $imdb->searchPeople($query) : [],
        ]);
    }

    public function show(Request $request, string $id, ImdbApiService $imdb): View
    {
        $person = $imdb->findPersonById($id);
        $creditFilter = (string) $request->string('credit_filter', 'all');
        $sort = (string) $request->string('sort', 'release_date');

        $creditBuckets = collect([
            'acting' => 'Acting credits',
            'directing' => 'Directing credits',
            'writing' => 'Writing credits',
            'producing' => 'Producing credits',
            'crew' => 'Crew credits',
        ])->mapWithKeys(fn (string $label, string $key): array => [$key => [
            'label' => $label,
            'items' => $this->creditsFor($person, $key, $sort),
        ]]);

        return view('people.show', [
            'person' => $person,
            'personTypes' => $this->personTypes(),
            'creditBuckets' => $creditBuckets,
            'creditFilter' => $creditFilter,
            'sort' => $sort,
        ]);
    }

    private function personTypes(): array
    {
        return [
            'Actor',
            'Director',
            'Writer',
            'Producer',
            'Composer',
            'Cinematographer',
            'Editor',
            'Costume designer',
            'Production designer',
            'Voice actor',
            'Stunt performer',
            'Casting director',
            'Executive producer',
            'Host / presenter',
            'Self / documentary appearance',
            'Multiple professions per person',
        ];
    }

    private function creditsFor(array $person, string $bucket, string $sort): array
    {
        $credits = collect(Arr::get($person, 'filmography', []));

        if ($credits->isEmpty()) {
            return [];
        }

        $filtered = $credits->filter(function (array $credit) use ($bucket): bool {
            $job = strtolower((string) Arr::get($credit, 'category', Arr::get($credit, 'job', '')));

            return match ($bucket) {
                'acting' => str_contains($job, 'actor') || str_contains($job, 'actress') || str_contains($job, 'cast') || str_contains($job, 'voice') || str_contains($job, 'self'),
                'directing' => str_contains($job, 'director'),
                'writing' => str_contains($job, 'writer') || str_contains($job, 'screenplay'),
                'producing' => str_contains($job, 'producer'),
                default => ! (str_contains($job, 'actor') || str_contains($job, 'actress') || str_contains($job, 'cast') || str_contains($job, 'voice') || str_contains($job, 'self') || str_contains($job, 'director') || str_contains($job, 'writer') || str_contains($job, 'screenplay') || str_contains($job, 'producer')),
            };
        });

        return $this->sortCredits($filtered, $sort)->values()->all();
    }

    private function sortCredits(Collection $credits, string $sort): Collection
    {
        if ($sort === 'rating') {
            return $credits->sortByDesc(fn (array $credit): float => (float) Arr::get($credit, 'rating', Arr::get($credit, 'averageRating', 0)));
        }

        if ($sort === 'popularity') {
            return $credits->sortByDesc(fn (array $credit): int => (int) Arr::get($credit, 'numVotes', Arr::get($credit, 'popularity', 0)));
        }

        return $credits->sortByDesc(function (array $credit): int {
            $releaseDate = Arr::get($credit, 'releaseDate');

            if ($releaseDate) {
                return Carbon::parse((string) $releaseDate)->timestamp;
            }

            return (int) Arr::get($credit, 'year', Arr::get($credit, 'startYear', 0));
        });
    }
}
