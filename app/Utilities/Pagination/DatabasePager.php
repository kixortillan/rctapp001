<?php

namespace App\Utilities\Pagination;

use App\Utilities\Pagination\Contracts\PagerInterface;

class DatabasePager implements PagerInterface
{
    protected $page;

    protected $perPage;

    protected $orderBy;

    protected $order;

    protected $search;

    protected $searchColumns = [];

    public function __construct(int $page, int $perPage,
        string $orderBy = '', string $order = 'desc',
        string $search = null, array $searchColumns = []) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->orderBy = $orderBy;
        $this->order = $order;
        $this->search = $search;
        $this->searchColumns = $searchColumns;
    }

    public function page()
    {
        return $this->page;
    }

    public function perPage()
    {
        return $this->perPage;
    }

    public function orderBy()
    {
        return $this->orderBy;
    }

    public function order()
    {
        return $this->order;
    }

    public function searchColumns()
    {
        return $this->searchColumns;
    }

    public function search()
    {
        return $this->search;
    }
}
