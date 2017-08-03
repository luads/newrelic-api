<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\Error;

use Psr\Http\Message\ResponseInterface;

class ApiErrorHandler
{
    /**
     * @param ResponseInterface $response
     *
     * @throws ApiException
     */
    public function handle(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 200) {
            // This is a valid response, there was no error.
            return;
        }

        switch ($response->getStatusCode()) {
            case 401:
                $errorMessage = 'Invalid or no API key given.';
                break;
            case 403:
                $errorMessage = 'API key does not have sufficient permissions.';
                break;
            case 422:
                $errorMessage = 'Internal error occurred while trying to access endpoint.';
                break;
            case 500:
                $errorMessage = 'A server error occurred, please contact New Relic support.';
                break;
            default:
                $errorMessage = 'An unknown error occurred.';
        }

        throw new ApiException($errorMessage);
    }
}
