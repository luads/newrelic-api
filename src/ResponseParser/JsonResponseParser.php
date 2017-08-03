<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\ResponseParser;

use Psr\Http\Message\ResponseInterface;

class JsonResponseParser extends AbstractResponseParser
{
    /**
     * @inheritdoc
     */
    public function parse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents());
    }
}
