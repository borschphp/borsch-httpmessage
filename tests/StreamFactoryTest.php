<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\Stream;
use Borsch\Message\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamFactoryTest extends TestCase
{

    public function testCreateStream()
    {
        $content = 'content';
        $stream = (new StreamFactory())->createStream($content);
        $this->assertEquals($content, $stream->getContents());
        $this->assertInstanceOf(Stream::class, $stream);
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    public function testCreateStreamFromResource()
    {
        $stream = (new StreamFactory())->createStreamFromResource(fopen('php://temp', 'wb+'));
        $this->assertEquals('php://temp', $stream->getMetadata('uri'));
        $this->assertInstanceOf(Stream::class, $stream);
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    public function testCreateStreamFromFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = (new StreamFactory())->createStreamFromFile($file);
        $this->assertEquals($file, $stream->getMetadata('uri'));
        $this->assertInstanceOf(Stream::class, $stream);
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
}
