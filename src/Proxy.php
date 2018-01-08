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

        // Overwrite target scheme and host.
        $uri = $this->request->getUri()
            ->withScheme($target->getScheme())
            ->withHost($target->getHost());

        // Check for custom port.
        if ($port = $target->getPort()) {
            $uri = $uri->withPort($port);
        }

        // Check for subdirectory.
        if ($path = $target->getPath()) {
            $uri = $uri->withPath(rtrim($path, '/') . '/' . ltrim($uri->getPath(), '/'));
        }

        /** @var MessageInterface $request */
        $request = $this->request->withUri($uri);

        /** @var array $stack */
        $stack = $this->filters;
        $stack[] = function (RequestInterface $request, ResponseInterface $response, callable $next) {
            $response = $this->adapter->send($request);
            return $next($request, $response);
        };

        /** @var Relay $relay */
        $relay = (new RelayBuilder)->newInstance($stack);
        return $relay($request, new Response);
    }
}
