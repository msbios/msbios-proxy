<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */
namespace MSBios\Proxy\Adapter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class DummyAdapter
 * @package MSBios\Proxy\Adapter
 */
class DummyAdapter implements AdapterInterface
{

    /**
     * Send the request and return the response.
     *
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    public function send(RequestInterface $request)
    {
        return new Response(
            $request->getBody(),
            \Zend\Http\Response::STATUS_CODE_200
        );
    }
}
