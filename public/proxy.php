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

// Forward the request and get the response.
// $response = $proxy->forward($request)->to('https://someapprentice.github.io');
// $response = $proxy->forward($request)->to('http://unba.org.ua');
$response = $proxy->forward($request)->to('http://open-power-dev.demo.gns-it.com');

echo $response->getBody()->__toString(); die();

// Output response to the browser.
// echo (new Zend\Diactoros\Response\SapiEmitter)->emit($response);