<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

use Proxy\Proxy;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Proxy\Filter\RemoveEncodingFilter;
use Zend\Diactoros\ServerRequestFactory;

// Create a PSR7 request based on the current browser request.
$request = ServerRequestFactory::fromGlobals();

// Create a guzzle client
$guzzle = new GuzzleHttp\Client;

// Create the proxy instance
$proxy = new Proxy(new GuzzleAdapter($guzzle));

// Add a response filter that removes the encoding headers.
$proxy->filter(new RemoveEncodingFilter);

// Forward the request and get the response.
$response = $proxy
    ->forward($request)
    ->filter(function ($request, $response, $next) {
        // Manipulate the request object.
        $request = $request->withHeader('User-Agent', 'FishBot/1.0');

        // Call the next item in the middleware.
        $response = $next($request, $response);

        // Manipulate the response object.
        $response = $response->withHeader('X-Proxy-Foo', 'Bar');

        return $response;
    })
    ->to('https://gns-it.com');

// Output response to the browser.
(new Zend\Diactoros\Response\SapiEmitter)
    ->emit($response);