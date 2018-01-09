<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */
namespace MSBiosTest\Proxy;

use MSBios\Proxy\Module;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleTest
 * @package MSBiosTest\Proxy
 */
class ModuleTest extends TestCase
{

    /**
     * @return $this
     */
    public function testGetModuleConfig()
    {
        $this->assertInternalType('array', (new Module)->getConfig());
        return $this;
    }

    /**
     * @return $this
     */
    public function testGetAutoloaderConfig()
    {
        $this->assertInternalType('array', (new Module)->getAutoloaderConfig());
        return $this;
    }
}
