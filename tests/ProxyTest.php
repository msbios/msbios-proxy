<?php
/**
 * @access protected
 * @author Judzhin Miles <info[woof-woof]msbios.com>
 */

namespace MSBiosTest\Proxy;

use MSBios\Proxy\Adapter\AdapterInterface;
use MSBios\Proxy\Adapter\DummyAdapter;
use MSBios\Proxy\Exception\UnexpectedValueException;
use MSBios\Proxy\Proxy;
use MSBios\Proxy\ProxyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

/**
 * Class ProxyTest
 * @package MSBiosTest\Proxy
 */
class ProxyTest extends TestCase
{
    /** @var ProxyInterface */
    private $proxy;

    /**
     * @return $this
     */
    public function setUp()
    {
        $this->proxy = new Proxy(new DummyAdapter);
        return $this;
    }

    /**
     * @expectedException UnexpectedValueException
     * @return $this
     */
    public function testToThrowsExceptionIfNoRequestIsGiven()
    {
        $this->proxy->to('http://www.example.com');
        return $this;
    }

    /**
     * @return $this
     */
    public function testToReturnsPsrResponse()
    {
        /** @var ResponseInterface $response */
        $response = $this->proxy
            ->forward(ServerRequestFactory::fromGlobals())
            ->to('http://www.example.com');

        $this->assertInstanceOf(ResponseInterface::class, $response);

        return $this;
    }

    /**
     * @return $this
     */
    public function testToAppliesFilters()
    {
        /** @var boolean $applied */
        $applied = false;
        $this->proxy
            ->forward(ServerRequestFactory::fromGlobals())
            ->filter(function (RequestInterface $request, ResponseInterface $response) use (&$applied) {
                $applied = true;
            })->to('http://www.example.com');
        $this->assertTrue($applied);

        return $this;
    }

    /**
     * @return $this
     */
    public function testToSendsRequest()
    {
        /** @var RequestInterface $request */
        $request = new Request('http://localhost/path?query=yes', 'GET');

        /** @var string $url */
        $url = 'https://www.example.com';

        /** @var \PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockBuilder(DummyAdapter::class)
            ->getMock();

        /** @var \PHPUnit_Framework_SelfDescribing $verifyParam */
        $verifyParam = $this->callback(function (RequestInterface $request) use ($url) {
            return $request->getUri() == 'https://www.example.com/path?query=yes';
        });

        $mock->expects($this->once())
            ->method('send')
            ->with($verifyParam)
            ->willReturn(new Response);

        /** @var ProxyInterface $proxy */
        $proxy = new Proxy($mock);
        $proxy->forward($request)->to($url);

        return $this;
    }

    /**
     * @return $this
     */
    public function testToSendsRequestWithPort()
    {
        /** @var RequestInterface $request */
        $request = new Request('http://localhost/path?query=yes', 'GET');

        /** @var string $url */
        $url = 'https://www.example.com:3000';

        /** @var \PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockBuilder(DummyAdapter::class)
            ->getMock();

        /** @var \PHPUnit_Framework_SelfDescribing $verifyParam */
        $verifyParam = $this->callback(function (RequestInterface $request) use ($url) {
            return $request->getUri() == 'https://www.example.com:3000/path?query=yes';
        });

        $mock->expects($this->once())
            ->method('send')
            ->with($verifyParam)
            ->willReturn(new Response);

        /** @var ProxyInterface $proxy */
        $proxy = new Proxy($mock);
        $proxy->forward($request)->to($url);

        return $this;
    }

    /**
     * @return $this
     */
    public function testToSendsRequestWithSubdirectory()
    {
        /** @var RequestInterface $request */
        $request = new Request('http://localhost/path?query=yes', 'GET');
        /** @var string $url */
        $url = 'https://www.example.com/proxy/';

        /** @var \PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockBuilder(DummyAdapter::class)
            ->getMock();

        /** @var \PHPUnit_Framework_SelfDescribing $verifyParam */
        $verifyParam = $this->callback(function (RequestInterface $request) use ($url) {
            return $request->getUri() == 'https://www.example.com/proxy/path?query=yes';
        });

        $mock->expects($this->once())
            ->method('send')
            ->with($verifyParam)
            ->willReturn(new Response);

        /** @var ProxyInterface $proxy */
        $proxy = new Proxy($mock);
        $proxy->forward($request)->to($url);

        return $this;
    }
}
