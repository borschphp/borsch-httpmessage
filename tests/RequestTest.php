<?php
/**
 * @author    Alexandre DEBUSSCHÈRE <alexandre@kosmonaft.dev>
 * @copyright 2021 Kosmonaft
 * @license   Commercial
 */

namespace BorschTest\Message;

use Borsch\Message\Request;
use Borsch\Message\RequestFactory;
use Borsch\Message\UriFactory;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testWithMethod()
    {
        $request = new Request();
        $this->assertEquals('GET', $request->getMethod());
        $request = $request->withMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
        $request = $request->withMethod('put');
        $this->assertEquals('PUT', $request->getMethod());
    }

    public function testGetRequestTargetWithoutQueries()
    {
        $request = (new RequestFactory())->createRequest('POST', 'https://example.com/api/v1/dog');
        $this->assertEquals('/api/v1/dog', $request->getRequestTarget());
    }

    public function testGetRequestTargetWithQueries()
    {
        $request = (new RequestFactory())->createRequest('POST', 'https://example.com/api/v1/dog?test=1&foo=bar');
        $this->assertEquals('/api/v1/dog?test=1&foo=bar', $request->getRequestTarget());
    }

    public function testGetRequestTargetWithoutPathNorQueries()
    {
        $request = (new RequestFactory())->createRequest('GET', '');
        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function testGetMethod()
    {
        $request = new Request();
        $this->assertEquals('GET', $request->getMethod());
        $request = $request->withMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testWithUri()
    {
        $uri = (new UriFactory())->createUri('https://example.com/api/v1/dog');
        $request = new Request();
        $request = $request->withUri($uri);
        $this->assertEquals($uri, $request->getUri());
    }

    public function testWithRequestTarget()
    {
        $target = '/api/v1/dog';
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com/api/v1/dog');
        $this->assertEquals($target, $request->getRequestTarget());
        $request = $request->withRequestTarget($target);
        $this->assertEquals($target, $request->getRequestTarget());
        $request = $request->withRequestTarget('admin/profile');
        $this->assertEquals('admin/profile', $request->getRequestTarget());
    }

    public function testGetUri()
    {
        $uri = (new UriFactory())->createUri('https://example.com/api/v1/dog');
        $request = (new RequestFactory())->createRequest('GET', $uri);
        $this->assertEquals($uri, $request->getUri());
        $uri = (new UriFactory())->createUri('https://example.com/admin/profile');
        $this->assertNotEquals($uri, $request->getUri());
    }
}
