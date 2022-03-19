<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\Message;
use Borsch\Message\Stream;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;
use TypeError;

class MessageTest extends TestCase
{


    public function testWithAddedHeader()
    {
        $message = new Message();
        $message = $message
            ->withHeader('X-Test', 'header_01')
            ->withAddedHeader('x-test', 'header_02');

        $this->assertTrue($message->hasHeader('x-TESt'));
        $this->assertStringContainsString('header_02', $message->getHeaderLine('x-tEsT'));
    }

    public function testWithValidProtocolVersion()
    {
        $message = new Message();
        $message = $message->withProtocolVersion('2.0');

        $this->assertEquals('2.0', $message->getProtocolVersion());
    }

    public function testWithInvalidProtocolVersionThrowsException()
    {
        $message = new Message();
        $this->expectException(InvalidArgumentException::class);
        $message->withProtocolVersion('2.42');
    }

    public function testGetHeaderLine()
    {
        $message = new Message();
        $message = $message
            ->withHeader('X-Test', 'header_01')
            ->withAddedHeader('x-test', 'header_02');

        $this->assertTrue($message->hasHeader('x-TESt'));
        $this->assertEquals('header_01, header_02', $message->getHeaderLine('x-tEsT'));
    }

    public function testGetHeaderLineWithoutHeaders()
    {
        $message = new Message();
        $this->assertEquals('', $message->getHeaderLine('x-tEsT'));
    }

    public function testWithoutHeader()
    {
        $message = new Message();
        $message = $message
            ->withHeader('X-Test', 'header_01')
            ->withHeader('x-Foo', 'header_02');

        $this->assertTrue($message->hasHeader('x-TESt'));
        $message = $message->withoutHeader('x-teSt');
        $this->assertFalse($message->hasHeader('x-test'));
    }

    public function testWithValidBody()
    {
        $message = new Message();
        $message = $message->withBody(new Stream());

        $this->assertInstanceOf(StreamInterface::class, $message->getBody());
    }

    public function testWithInvalidBodyThrowsException()
    {
        $message = new Message();
        $this->expectException(TypeError::class);
        $message->withBody('');
    }

    public function testGetBody()
    {
        $message = new Message();
        $stream = new Stream(fopen('php://temp', 'r'));

        $message = $message->withBody($stream);

        $this->assertEquals($stream, $message->getBody());
    }

    public function testGetHeader()
    {
        $message = new Message();
        $message = $message
            ->withHeader('X-Test', 'header_01')
            ->withHeader('x-foo', ['header_02', 'header_03']);

        $this->assertTrue($message->hasHeader('x-TESt'));
        $this->assertTrue($message->hasHeader('X-FOO'));
        $this->assertIsArray($message->getHeader('x-tEsT'));
        $this->assertIsArray($message->getHeader('X-FoO'));
        $this->assertCount(1, $message->getHeader('x-tEsT'));
        $this->assertCount(2, $message->getHeader('x-foO'));
        $this->assertContains('header_02', $message->getHeader('x-FoO'));
        $this->assertContains('header_03', $message->getHeader('x-FoO'));
        $this->assertContains('header_01', $message->getHeader('X-TEST'));
    }

    public function testHasHeader()
    {
        $message = new Message();
        $message = $message
            ->withHeader('X-Test', 'header_01')
            ->withHeader('x-foo', ['header_02', 'header_03']);

        $this->assertTrue($message->hasHeader('x-test'));
        $this->assertTrue($message->hasHeader('X-FOO'));
        $this->assertFalse($message->hasHeader('random'));
    }

    public function testWithHeader()
    {
        $message = new Message();
        $message = $message
            ->withHeader('X-Test', 'header_01')
            ->withHeader('x-foo', ['header_02', 'header_03']);

        $this->assertTrue($message->hasHeader('x-TESt'));
        $this->assertTrue($message->hasHeader('X-FOO'));
    }

    public function testWithHeaderNotStringThrowsException()
    {
        $message = new Message();
        $this->expectException(InvalidArgumentException::class);
        $message
            ->withHeader('X-Test', new stdClass());
    }

    public function testWithHeaderNotStringArrayThrowsException()
    {
        $message = new Message();
        $this->expectException(InvalidArgumentException::class);
        $message
            ->withHeader('X-Test', ['header_02', new stdClass()]);
    }

    public function testWithHeaderEmptyArrayThrowsException()
    {
        $message = new Message();
        $this->expectException(InvalidArgumentException::class);
        $message
            ->withHeader('X-Test', []);
    }

    public function testGetProtocolVersion()
    {
        $message = new Message();
        $message = $message->withProtocolVersion('2.0');
        $this->assertEquals('2.0', $message->getProtocolVersion());
        $message = $message->withProtocolVersion('1.0');
        $this->assertEquals('1.0', $message->getProtocolVersion());
    }

    public function testGetHeaders()
    {
        $message = new Message();
        $message = $message
            ->withHeader('X-Test', 'header_01')
            ->withHeader('x-foo', ['header_02', 'header_03']);

        $this->assertIsArray($message->getHeaders());
        $this->assertCount(2, $message->getHeaders());
        $this->assertArrayHasKey('X-Test', $message->getHeaders());
        $this->assertArrayHasKey('x-foo', $message->getHeaders());
        $this->assertArrayNotHasKey('X-FOO', $message->getHeaders());
        $this->assertArrayNotHasKey('rAndOm', $message->getHeaders());
    }
}
