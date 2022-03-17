<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\ServerRequest;
use Borsch\Message\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactoryTest extends TestCase
{

    public function testCreateServerRequest()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $this->assertInstanceOf(ServerRequest::class, $server_request);
        $this->assertInstanceOf(ServerRequestInterface::class, $server_request);
        $this->assertEquals([], $server_request->getServerParams());
        $this->assertEquals([], $server_request->getUploadedFiles());
        $this->assertEquals([], $server_request->getCookieParams());
        $this->assertEquals([], $server_request->getQueryParams());
        $this->assertNull($server_request->getParsedBody());
        $this->assertEquals([], $server_request->getAttributes());
        $this->assertEquals('php://temp', $server_request->getBody()->getMetadata('uri'));

        $server_params = [
            'HTTP_HOST' => 'example.com',
            'CONTENT_TYPE' => 'text/html; charset=UTF-8',
        ];
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com', $server_params);
        $this->assertInstanceOf(ServerRequest::class, $server_request);
        $this->assertInstanceOf(ServerRequestInterface::class, $server_request);
        $this->assertSame($server_params, $server_request->getServerParams());
        $this->assertSame([], $server_request->getUploadedFiles());
        $this->assertSame([], $server_request->getCookieParams());
        $this->assertSame([], $server_request->getQueryParams());
        $this->assertNull($server_request->getParsedBody());
        $this->assertSame([], $server_request->getAttributes());
        $this->assertSame('php://temp', $server_request->getBody()->getMetadata('uri'));
    }
}
