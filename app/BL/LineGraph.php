<?php

namespace App\BL;

use App\BL\GraphRanges;

class LineGraph
{
    protected $mode;

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function generateX()
    {
        $generator = new GraphRanges();

        switch ($this->mode) {
            case 'yearly':
                return $generator->monthsInYear();

            case 'monthly':
                return $generator->daysInMonth();

            case 'weekly':
                return $generator->daysInWeek();

            default:
            case 'daily':
                return $generator->wholeDay();
        }
    }

    public function generateY(array $data)
    {

    }
}
