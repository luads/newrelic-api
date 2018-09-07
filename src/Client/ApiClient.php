<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\Client;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use TreeHouse\NewRelicApi\Error\ApiErrorHandler;
use TreeHouse\NewRelicApi\Error\ApiException;
use TreeHouse\NewRelicApi\ResponseParser\JsonResponseParser;
use TreeHouse\NewRelicApi\ResponseParser\ResponseParserInterface;
use TreeHouse\NewRelicApi\ResponseParser\XmlResponseParser;

class ApiClient
{
    const API_URL = 'https://api.newrelic.com/v2';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ApiErrorHandler
     */
    private $errorHandler;

    /**
     * @var Filter[]
     */
    private $filters;

    /**
     * @var string
     */
    private $format = 'json';


    /**
     * @var ResponseParserInterface
     */
    public $responseParser;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client();
        $this->errorHandler = new ApiErrorHandler();
        $this->setResponseParser();
    }

    /**
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[$filter->getName()] = $filter->getValue();
    }

    /**
     * Validate a user-given API URI.
     *
     * @param string $uri
     *
     * @return bool
     */
    private function isValidUri(string $uri) : bool
    {
        if (preg_match('/^https?:/', $uri) === 1) {
            return false;
        }

        if (preg_match('/(\.json|\.xml)$/', $uri)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $uri     The URI of the API endpoint.
     *                        Do not include the .json or .xml suffix, use setFormat to change the format instead.
     * @param string $method  The HTTP method to use (GET, POST, PUT, DELETE)
     * @param array  $headers Any additional headers to send with the request.
     * @param array  $json    JSON data to send with POST or PUT requests
     *
     * @throws ApiException
     *
     * @return ResponseInterface
     */
    public function request(string $uri, string $method = 'GET', array $headers = [], array $json = []) : ResponseInterface
    {
        if (!$this->isValidUri($uri)) {
            throw new \InvalidArgumentException(sprintf(
                'The given URI "%s" is invalid. ' .
                'Please make sure you are only passing the endpoint like "/applications", not the full API URL.' .
                'Also omit any suffix like .json or .xml.',
                $uri
            ));
        }

        $headers = array_merge($headers, ['X-Api-Key' => $this->apiKey]);
        $filters = !empty($this->filters) ? http_build_query($this->filters) : null;
        $url = sprintf('%s%s.%s?%s', self::API_URL, $uri, $this->format, $filters);
        
        $options = ['headers' => $headers];
        
        if (!empty($json)) {
            $options['json'] = $json;
        }
        
        $url = sprintf('%s%s.%s?%s', self::API_URL, $uri, $this->format, $filters);

        $response = $this->client->request($method, $url, $options);
        $this->errorHandler->handle($response);

        return $response;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    /**
     * Sets the ResponseParser to use based on the chosen format.
     */
    private function setResponseParser()
    {
        switch (strtolower($this->format)) {
            case 'json':
                $this->responseParser = new JsonResponseParser();
                break;
            case 'xml':
                $this->responseParser = new XmlResponseParser();
                break;
            default:
                throw new \RuntimeException(sprintf(
                    'Unknown format "%s" set in ApiClient. Use either "json" or "xml".',
                    $this->format
                ));
        }
    }
}
