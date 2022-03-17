<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\ServerRequest;
use Borsch\Message\ServerRequestFactory;
use Borsch\Message\Stream;
use Borsch\Message\UploadedFileFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use stdClass;

class ServerRequestTest extends TestCase
{

    public function testWithoutAttribute()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $this->assertEquals($server_request, $server_request->withoutAttribute('random'));

        $server_request = $server_request->withAttribute('Foo', 'Bar');
        $this->assertNotEquals($server_request, $server_request->withoutAttribute('Foo'));

        $server_request = $server_request->withoutAttribute('Foo');
        $this->assertArrayNotHasKey('Foo', $server_request->getAttributes());
    }

    public function testGetAttributes()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request
            ->withAttribute('Foo', 'Bar')
            ->withAttribute('Baz', 42);

        $this->assertCount(2, $server_request->getAttributes());
        $this->assertArrayHasKey('Foo', $server_request->getAttributes());
        $this->assertArrayHasKey('Baz', $server_request->getAttributes());
        $this->assertEquals('Bar', $server_request->getAttributes()['Foo']);
        $this->assertEquals(42, $server_request->getAttributes()['Baz']);
    }

    public function testGetAttribute()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request
            ->withAttribute('Foo', 'Bar')
            ->withAttribute('Baz', 42);

        $this->assertCount(2, $server_request->getAttributes());
        $this->assertArrayHasKey('Foo', $server_request->getAttributes());
        $this->assertArrayHasKey('Baz', $server_request->getAttributes());
        $this->assertEquals('Bar', $server_request->getAttribute('Foo'));
        $this->assertEquals(42, $server_request->getAttribute('Baz'));
    }

    public function testWithAttribute()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request
            ->withAttribute('Foo', 'Bar')
            ->withAttribute('Baz', 42);

        $this->assertCount(2, $server_request->getAttributes());
        $this->assertArrayHasKey('Foo', $server_request->getAttributes());
        $this->assertArrayHasKey('Baz', $server_request->getAttributes());
        $this->assertEquals('Bar', $server_request->getAttribute('Foo'));
        $this->assertEquals(42, $server_request->getAttribute('Baz'));
    }

    public function testGetParsedBody()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request
            ->withParsedBody(json_decode(json_encode(['Foo' => 'Bar']), true));

        $this->assertIsArray($server_request->getParsedBody());
        $this->assertArrayHasKey('Foo', $server_request->getParsedBody());
        $this->assertArrayNotHasKey('Baz', $server_request->getParsedBody());
    }

    public function testGetServerParams()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com',
            [
                'Foo' => 'Bar',
                'Baz' => 42
            ]
        );

        $this->assertIsArray($server_request->getServerParams());
        $this->assertCount(2, $server_request->getServerParams());
        $this->assertEquals('Bar', $server_request->getServerParams()['Foo']);
        $this->assertEquals(42, $server_request->getServerParams()['Baz']);
    }

    public function testGetCookieParams()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request->withCookieParams([
            'Foo' => 'Bar',
            'Baz' => 42
        ]);

        $this->assertIsArray($server_request->getCookieParams());
        $this->assertCount(2, $server_request->getCookieParams());
        $this->assertEquals('Bar', $server_request->getCookieParams()['Foo']);
        $this->assertEquals(42, $server_request->getCookieParams()['Baz']);
    }

    public function testGetQueryParams()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request->withQueryParams([
            'Foo' => 'Bar',
            'Baz' => 42
        ]);

        $this->assertIsArray($server_request->getQueryParams());
        $this->assertCount(2, $server_request->getQueryParams());
        $this->assertEquals('Bar', $server_request->getQueryParams()['Foo']);
        $this->assertEquals(42, $server_request->getQueryParams()['Baz']);
    }

    public function testWithParsedBody()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request
            ->withParsedBody(json_decode(json_encode(['Foo' => 'Bar']), true));

        $this->assertIsArray($server_request->getParsedBody());
        $this->assertArrayHasKey('Foo', $server_request->getParsedBody());
        $this->assertArrayNotHasKey('Baz', $server_request->getParsedBody());
    }

    public function testGetUploadedFiles()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request
            ->withUploadedFiles([
                (new UploadedFileFactory())->createUploadedFile(new Stream(fopen('php://temp', 'r')))
            ]);

        $this->assertIsArray($server_request->getUploadedFiles());
        $this->assertCount(1, $server_request->getUploadedFiles());
        $this->assertContainsOnlyInstancesOf(UploadedFileInterface::class, $server_request->getUploadedFiles());
    }

    public function testWithCookieParams()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request->withCookieParams([
            'Foo' => 'Bar',
            'Baz' => 42
        ]);

        $this->assertIsArray($server_request->getCookieParams());
        $this->assertCount(2, $server_request->getCookieParams());
        $this->assertEquals('Bar', $server_request->getCookieParams()['Foo']);
        $this->assertEquals(42, $server_request->getCookieParams()['Baz']);
    }

    public function testWithValidUploadedFiles()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request
            ->withUploadedFiles([
                (new UploadedFileFactory())->createUploadedFile(new Stream(fopen('php://temp', 'r')))
            ]);

        $this->assertIsArray($server_request->getUploadedFiles());
        $this->assertCount(1, $server_request->getUploadedFiles());
        $this->assertContainsOnlyInstancesOf(UploadedFileInterface::class, $server_request->getUploadedFiles());
    }

    public function testWithInvalidUploadedFiles()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $server_request
            ->withUploadedFiles([
                (new UploadedFileFactory())->createUploadedFile(new Stream(fopen('php://temp', 'r'))),
                new stdClass()
            ]);
    }

    public function testWithQueryParams()
    {
        $server_request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.com');
        $server_request = $server_request->withQueryParams([
            'Foo' => 'Bar',
            'Baz' => 42
        ]);

        $this->assertIsArray($server_request->getQueryParams());
        $this->assertCount(2, $server_request->getQueryParams());
        $this->assertEquals('Bar', $server_request->getQueryParams()['Foo']);
        $this->assertEquals(42, $server_request->getQueryParams()['Baz']);
    }
}
