<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\ResponseParser;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractResponseParser implements ResponseParserInterface
{
    /**
     * @inheritdoc
     */
    public function getPages(ResponseInterface $response)
    {
        $linkHeader = $response->getHeader('Link');

        if (count($linkHeader) > 1) {
            throw new \RuntimeException('API response has multiple Link headers, cannot determine page information.');
        }

        $pages = [
            'first' => ['url' => null, 'pagenumber' => null],
            'prev' => ['url' => null, 'pagenumber' => null],
            'next' => ['url' => null, 'pagenumber' => null],
            'last' => ['url' => null, 'pagenumber' => null]
        ];

        if (!empty($linkHeader)) {
            foreach (explode(',', reset($linkHeader)) as $link) {
                // Dissect the header value
                preg_match('/<(?P<url>[^>]+)>;\srel="(?P<rel>[a-z]+)"/', $link, $matches);

                // Check if we want to save this value
                if (array_key_exists('rel', $matches) && in_array($matches['rel'], array_keys($pages))) {
                    $pages[$matches['rel']]['url'] = $matches['url'];
                    parse_str(parse_url($matches['url'])['query'], $query);
                    $pages[$matches['rel']]['pagenumber'] = (int)$query['page'];
                }
            }
        }

        return $pages;
    }

    /**
     * @inheritdoc
     */
    public function hasNextPage(ResponseInterface $response)
    {
        $pages = $this->getPages($response);

        return ($pages['next']['pagenumber'] !== null);
    }
}
