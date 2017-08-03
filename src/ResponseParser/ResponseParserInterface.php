<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\ResponseParser;

use Psr\Http\Message\ResponseInterface;

interface ResponseParserInterface
{
    /**
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function getPages(ResponseInterface $response);

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    public function hasNextPage(ResponseInterface $response);

    /**
     * @param ResponseInterface $response
     *
     * @return object
     */
    public function parse(ResponseInterface $response);
}
