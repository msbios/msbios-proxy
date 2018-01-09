<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */

namespace MSBios\Proxy\Filter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RemoveEncodingFilter
 * @package MSBios\Proxy\Filter
 */
class RemoveEncodingFilter implements FilterInterface
{
    const TRANSFER_ENCODING = 'transfer-encoding';
    const CONTENT_ENCODING = 'content-encoding';

    /**
     * Apply filter to request and/or response.
     *
     * @param RequestInterface $response
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        /** @var ResponseInterface $response */
        $response = $next($request, $response);
        return $response
            ->withoutHeader(self::TRANSFER_ENCODING)
            ->withoutHeader(self::CONTENT_ENCODING);
    }
}
