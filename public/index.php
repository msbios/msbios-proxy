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

/** @var \Psr\Http\Message\ServerRequestInterface $request */
$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals();

/** @var \GuzzleHttp\ClientInterface $client */
$client = new \GuzzleHttp\Client;

/** @var \MSBios\Proxy\ProxyInterface $proxy */
$proxy = new \MSBios\Proxy\Proxy(
    new \MSBios\Proxy\Adapter\GuzzleAdapter($client)
);

$proxy->filter(new \MSBios\Proxy\Filter\RemoveEncodingFilter);

// Forward the request and get the response.
$response = $proxy
    ->forward($request)
    ->filter(function ($request, $response, $next) {
        // Manipulate the request object.
        $request = $request->withHeader('User-Agent', 'FishBot/1.0');

        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $next($request, $response);

        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $response->withHeader('X-Proxy-Foo', 'Bar');


        $body = $response->getBody();
        /** @var string $contents */
        $contents = $body->getContents();

        $body->write(str_replace('https://gns-it.com/', '/', $contents));
        $response->withBody($body);

        return $response;
    })
    ->to('https://gns-it.com');

// Output response to the browser.
(new Zend\Diactoros\Response\SapiEmitter)->emit($response);