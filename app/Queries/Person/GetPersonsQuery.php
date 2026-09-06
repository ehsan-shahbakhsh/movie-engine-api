<?php

namespace App\Queries\Person;

use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetPersonsQuery
{
    public function execute(?string $search, int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        if (blank($search)) {
            return Person::query()
                ->with('media')
                ->paginate(page: $page);
        }

        return Person::search($search)
            ->query(static fn($query) => $query->with('media'))
            ->paginate(perPage: $perPage, page: $page);
    }
}
