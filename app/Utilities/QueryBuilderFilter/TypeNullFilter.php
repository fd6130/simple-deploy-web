<?php

namespace App\Utilities\QueryBuilderFilter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeNullFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->when(
            $value === 'null',
            fn($query) => $query->whereNull($property),
            fn($query) => $query->where($property, $value)
        );
    }
}
