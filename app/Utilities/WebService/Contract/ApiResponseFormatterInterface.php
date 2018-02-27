<?php

namespace App\Utilities\WebService\Contract;

interface ApiResponseFormatterInterface
{
    /**
     * Returns an array containing formatted data
     * ready to be JSON encoded
     *
     * @return array
     */
    public function output();
}
