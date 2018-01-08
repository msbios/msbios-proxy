<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */
namespace MSBios\Proxy;

use MSBios\Proxy\Adapter\AdapterInterface;

/**
 * Class Proxy
 * @package MSBios\Proxy
 */
class Proxy
{

    /** @var  AdapterInterface */
    protected $adapter;

    /**
     * Proxy constructor.
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

}