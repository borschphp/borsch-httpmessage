<?php
/**
 * @author debuss-a
 */

use Borsch\Message\Response;
use Borsch\Message\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseFactoryTest extends TestCase
{

    public function testCreateResponse()
    {
        $response = (new ResponseFactory())->createResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
        $this->assertEquals('php://temp', $response->getBody()->getMetadata('uri'));
        $this->assertEquals([], $response->getHeaders());
        $this->assertEquals('1.1', $response->getProtocolVersion());

        $response = (new ResponseFactory())->createResponse(404);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getReasonPhrase());
        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
        $this->assertEquals('php://temp', $response->getBody()->getMetadata('uri'));
        $this->assertEquals([], $response->getHeaders());
        $this->assertEquals('1.1', $response->getProtocolVersion());

        $response = (new ResponseFactory())->createResponse(404, 'Custom Phrase');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Custom Phrase', $response->getReasonPhrase());
        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
        $this->assertEquals('php://temp', $response->getBody()->getMetadata('uri'));
        $this->assertEquals([], $response->getHeaders());
        $this->assertEquals('1.1', $response->getProtocolVersion());
    }
}
