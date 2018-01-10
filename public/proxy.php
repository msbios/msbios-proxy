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
$response = $proxy->forward($request)
    // ->to('http://unba.org.ua');
    // ->to('http://open-power-dev.demo.gns-it.com');
    ->to('http://gns-it.com');

// Output response to the browser.
(new Zend\Diactoros\Response\SapiEmitter)->emit($response);