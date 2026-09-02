<?php

namespace App\Queries\Person;

use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetPersonsQuery
{
    public function execute(?string $search, int $page = 1): LengthAwarePaginator
    {
        return Person::query()
            ->with('media')
            ->when($search != null, static function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('original_name', 'like', "%$search%");
            })
            ->paginate(page: $page);
    }
}
