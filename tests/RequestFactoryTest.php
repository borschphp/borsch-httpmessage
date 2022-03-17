<?php
/**
 * @author debuss-a
 */

use Borsch\Message\Request;
use Borsch\Message\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class RequestFactoryTest extends TestCase
{

    public function testCreateRequest()
    {
        $request = (new RequestFactory())->createRequest('POST', 'https://example.com/admin?foo=bar');
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://example.com/admin?foo=bar', (string)$request->getUri());
        $this->assertEquals('foo=bar', $request->getUri()->getQuery());
        $this->assertEquals(['Host' => ['example.com']], $request->getHeaders());
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertInstanceOf(StreamInterface::class, $request->getBody());
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(Request::class, $request);
    }
}
