<?php

namespace App\Utilities\Pagination\Contracts;

interface PagerInterface
{
    public function page();

    public function perPage();

    public function orderBy();

    public function order();

    public function searchColumns();

    public function search();
}
