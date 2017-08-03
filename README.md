New Relic REST API PHP Library
------------------------------

This PHP library allows you to connect to the New Relic REST API (v2) and request and process data from it.

## Installation

The easiest way to install the library is by using the Composer package manager:

    composer require treehouselabs/newrelic-api

## Using the library

In order to send a request to the New Relic REST API, you will need a valid API key.
You can find your API key on the "API keys" tab of the "Account settings" page
(accessible from the drop-down menu on the top right of the New Relic interface).

Create an instance of the API Client and pass your API key as an argument.

```php
$client = new ApiClient('your-newrelic-api-key');
```

You can then start making requests to and process responses from the API.

Below are a few code samples showing how to use the library. 

### Example 1: Getting a list of Applications

The following code would retrieve all your applications from the API:

```php
<?php

use TreeHouse\NewRelicApi\Client\ApiClient;
use TreeHouse\NewRelicApi\Error\ApiException;

class MyClass
{
    public function getNewRelicApplications()
    {
        // Initiate the client, replacing "your-newrelic-api-key" with your actual key
        $client = new ApiClient('your-newrelic-api-key');
        
        try {
            // Send a request to the "/applications" endpoint. The .json suffix is attached by default.
            $response = $client->request('/applications');
        } catch (ApiException $exception) {
            // Something went wrong... handle the exception here
            return false;
        }
        
        // In case you'd want to use the XML endpoint instead, change the format for the client prior to the request:
        // $client->setFormat('xml');
        
        // Parse the response we received from the API
        $applications = $client->responseParser->parse($response);
        
        return $applications;
    }
}
```

### Example 2: Getting Alerts Violations (multiple pages)

Some requests return multiple pages of results. The response parser can tell you if there are any additional pages,
so you can handle them if required. For example, if you want to get the first 5 pages of violations
you could use something like this:

```php
<?php

use TreeHouse\NewRelicApi\Client\ApiClient;
use TreeHouse\NewRelicApi\Client\Page;
use TreeHouse\NewRelicApi\Error\ApiException;

class MyClass
{
    public function getAlertsViolations()
    {
        // This array will hold the responses from the API
        $violations = [];
        
        // Initiate the client, replacing "your-newrelic-api-key" with your actual key
        $client = new ApiClient('your-newrelic-api-key');
        
        // Send a request to the "/alerts_violations" endpoint. The .json suffix is attached by default.
        try {
            $response = $client->request('/alerts_violations');
        } catch (ApiException $exception) {
            // Something went wrong... handle the exception here
            return false;
        }
        
        // Get the first page of results
        $violations[] = $client->responseParser->parse($response);
        
        // Get an additional 4 pages (or as many as there are, if less)
        $pageNumber = 2;
        while ($client->responseParser->hasNextPage($response)) {
            // Stop after 5 pages
            if ($pageNumber > 5) {
                break;
            }
            
            // Create a page filter object to fetch the next page
            $page = new Page($pageNumber);
            $client->addFilter($page);
            
            // Get the result
            try {
                $response = $client->request('/alerts_violations');
            } catch (ApiException $exception) {
                // Something went wrong... handle the exception here
                break;
            }
            
            // Parse the response we received from the API
            $violations[] = $client->responseParser->parse($response);
            
            // Increase the page number for the next iteration
            $pageNumber++;
        }
        
        return $violations;
    }
}
```
