<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\ResponseParser;

use Psr\Http\Message\ResponseInterface;

class XmlResponseParser extends AbstractResponseParser
{
    /**
     * @inheritdoc
     */
    public function parse(ResponseInterface $response)
    {
        $xmlObject = simplexml_load_string(
            $response->getBody()->getContents(),
            'SimpleXMLElement',
            LIBXML_NOCDATA
        );
        $xml = json_encode($xmlObject);

        return json_decode($xml);
    }
}
