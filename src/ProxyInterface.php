<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */
namespace MSBios\Proxy;

use MSBios\Proxy\Exception\UnexpectedValueException;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response;

/**
 * Interface ProxyInterface
 * @package MSBios\Proxy
 */
interface ProxyInterface
{
    /**
     * Add filter middleware.
     *
     * @param callable $callable
     * @return $this
     */
    public function filter(callable $callable);

    /**
     * Prepare the proxy to forward a request instance.
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function forward(RequestInterface $request);

    /**
     * Forward the request to the target url and return the response.
     *
     * @param string $target
     * @throws UnexpectedValueException
     * @return Response
     */
    public function to($target);
}
