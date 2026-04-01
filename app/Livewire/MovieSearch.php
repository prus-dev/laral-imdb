<?php

namespace App\Livewire;

use App\Services\ImdbApiService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Search | LaraIMDb')]
class MovieSearch extends Component
{
    public string $query = '';

    public array $results = [];

    public function mount(?string $q = null): void
    {
        $this->query = trim((string) $q);

        if ($this->query !== '') {
            $this->search();
        }
    }

    public function updatedQuery(): void
    {
        $this->search();
    }

    public function search(): void
    {
        if ($this->query === '') {
            $this->results = [];

            return;
        }

        $this->results = app(ImdbApiService::class)->search($this->query);
    }

    public function render()
    {
        return view('livewire.movie-search');
    }
}
