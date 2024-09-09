<?php

namespace App\Utilities\Pagination;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Pagination
{
    /**
     * Paginate the data.
     *
     * @param $data
     * @param $request
     * @return Paginator
     * @throws \ErrorException
     */
    public static function paginate($data, $request): Paginator
    {
        $limit = $request->limit ?? 30;
        $page = $request->page ?? 1;
        $offSet = ($page * $limit) - $limit;

        if ($data instanceof EloquentBuilder || $data instanceof QueryBuilder || $data instanceof \Spatie\QueryBuilder\QueryBuilder ||  $data instanceof HasMany)
        {
            $result = $data->paginate($limit, ['*'], 'page', $page);

            return new Paginator($result->items(), $result->total(), $limit, $page);
        }
        elseif ($data instanceof EloquentCollection || $data instanceof SupportCollection)
        {
            return new Paginator($data, $data->count(), $limit, $page);
        }
        elseif (is_array($data) === true)
        {
            return new Paginator($data, count($data), $limit,  $page);
        }
        else
        {
            throw new \RuntimeException('Unable to paginate the data.');
        }
    }
}
