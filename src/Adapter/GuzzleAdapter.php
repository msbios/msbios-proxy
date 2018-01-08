<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */
namespace MSBios\Proxy\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleAdapter
 * @package MSBios\Proxy\Adapter
 */
class GuzzleAdapter implements AdapterInterface
{

    /** @var ClientInterface */
    protected $client;

    /**
     * GuzzleAdapter constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client;
    }

    /**
     * Send the request and return the response.
     *
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    public function send(RequestInterface $request)
    {
        return $this->client->send($request);
    }
}
