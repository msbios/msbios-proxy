<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */

namespace MSBios\Proxy;

use MSBios\Proxy\Adapter\AdapterInterface;
use MSBios\Proxy\Exception\UnexpectedValueException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Relay\Relay;
use Relay\RelayBuilder;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;

/**
 * Class Proxy
 * @package MSBios\Proxy
 */
class Proxy implements ProxyInterface
{

    /** @var  AdapterInterface */
    protected $adapter;

    /** @var  RequestInterface */
    protected $request;

    /** @var callable[] */
    protected $filters = [];

    /**
     * Proxy constructor.
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Add filter middleware.
     *
     * @param callable $callable
     * @return $this
     */
    public function filter(callable $callable)
    {
        $this->filters[] = $callable;
        return $this;
    }

    /**
     * Prepare the proxy to forward a request instance.
     *
     * @param  RequestInterface $request
     * @return $this
     */
    public function forward(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Forward the request to the target url and return the response.
     *
     * @param  string $target
     * @throws UnexpectedValueException
     * @return Response
     */
    public function to($target)
    {
        if (is_null($this->request)) {
            throw new UnexpectedValueException('Missing request instance.');
        }

        /** @var UriInterface $target */
        $target = new Uri($target);

        /** @var UriInterface $uri */
        $uri = $this->request->getUri()
            ->withScheme($target->getScheme())
            ->withHost($target->getHost())
            ->withPort($target->getPort());

        // Check for subdirectory.
        if ($path = $target->getPath()) {
            /** @var UriInterface $uri */
            $uri = $uri->withPath(rtrim($path, '/') . '/' . ltrim($uri->getPath(), '/'));
        }

        /** @var RequestInterface $request */
        $request = $this->request->withUri($uri);

        /** @var callable[] $stack */
        $stack = $this->filters;
        $stack[] = function (RequestInterface $request, ResponseInterface $response, callable $next) {
            /** @var ResponseInterface $response */
            $response = $this->adapter->send($request);
            return $next($request, $response);
        };

        /** @var Relay $relay */
        $relay = (new RelayBuilder)->newInstance($stack);
        return $relay($request, new Response);
    }
}
