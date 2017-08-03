<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\Test\ResponseParser;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TreeHouse\NewRelicApi\ResponseParser\XmlResponseParser;

class XmlResponseParserTest extends TestCase
{
    public function testGetPages()
    {
        $responseParser = new XmlResponseParser();
        $pages = $responseParser->getPages($this->mockResponse());

        $baseUrl = 'https://api.newrelic.com/v2/applications/5313884/metrics.xml';

        // Test first page
        $this->assertSame(sprintf('%s?page=1', $baseUrl), $pages['first']['url']);
        $this->assertSame(1, $pages['first']['pagenumber']);

        // Test previous page
        $this->assertSame(sprintf('%s?page=18', $baseUrl), $pages['prev']['url']);
        $this->assertSame(18, $pages['prev']['pagenumber']);

        // Test next page
        $this->assertSame(sprintf('%s?page=20', $baseUrl), $pages['next']['url']);
        $this->assertSame(20, $pages['next']['pagenumber']);

        // Test last page
        $this->assertSame(sprintf('%s?page=20', $baseUrl), $pages['last']['url']);
        $this->assertSame(20, $pages['last']['pagenumber']);
    }

    public function testHasNextPage()
    {
        $responseParser = new XmlResponseParser();
        $this->assertTrue($responseParser->hasNextPage($this->mockResponse()));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Response
     */
    private function mockResponse()
    {
        // Dummy Link header with some page information
        $linkHeader = [
            '<https://api.newrelic.com/v2/applications/5313884/metrics.xml?page=1>; rel="first", ' .
            '<https://api.newrelic.com/v2/applications/5313884/metrics.xml?page=18>; rel="prev", ' .
            '<https://api.newrelic.com/v2/applications/5313884/metrics.xml?page=20>; rel="next", ' .
            '<https://api.newrelic.com/v2/applications/5313884/metrics.xml?page=20>; rel="last"'
        ];

        $mock = $this->createMock(Response::class);
        $mock->method('getHeader')->with('Link')->willReturn($linkHeader);

        return $mock;
    }
}
