<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\Client;

class Page extends Filter
{
    /**
     * @param int $pageNumber
     */
    public function __construct(int $pageNumber)
    {
        parent::__construct('page', $pageNumber);
    }
}
