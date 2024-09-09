<?php

namespace App\Utilities\Pagination;

use Illuminate\Support\Collection;

class Paginator extends \Illuminate\Pagination\Paginator
{
    protected $total;

    protected $lastPage;

    public function __construct($items, $total, $perPage, $currentPage = null, array $options = [])
    {
        $this->options = $options;

        foreach ($options as $key => $value)
        {
            $this->{$key} = $value;
        }

        $this->total = $total;
        $this->perPage = $perPage;
        $this->lastPage = max((int) ceil($total / $perPage), 1);
        $this->currentPage = $this->setCurrentPage($currentPage);
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : $this->path;

        $this->setItems($items);
    }

    public function total()
    {
        return (int) $this->total;
    }

    public function lastPage()
    {
        return (int) $this->lastPage;
    }

    public function toArray()
    {
        return [
            'current_page' => (int) $this->currentPage(),
            'from' => (int) $this->firstItem(),
            'per_page' => (int) $this->perPage(),
            'to' => (int) $this->lastItem(),
            'last_page' => (int) $this->lastPage(),
            'total' => (int) $this->total()
        ];
    }

    protected function setItems($items)
    {
        $this->items = $items instanceof Collection ? $items : Collection::make($items);

        $this->hasMore = $this->items->count() > $this->perPage;

        $offset = abs(($this->currentPage - 1) * $this->perPage);

        $this->items = $this->items->slice($offset < $this->items->count() ? $offset : 0, $this->perPage)->values();
    }
}
