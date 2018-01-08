<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */
namespace MSBios\Proxy\Adapter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface AdapterInterface
 * @package MSBios\Proxy\Adapter
 */
interface AdapterInterface
{
    /**
     * Send the request and return the response.
     *
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    public function send(RequestInterface $request);
}