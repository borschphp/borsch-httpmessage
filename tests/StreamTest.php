<?php
/**
 * @author debuss-a
 */

use Borsch\Message\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{

    public function testIsSeekable()
    {
        $stream = new Stream();
        $this->assertTrue($stream->isSeekable());
    }

    public function testIsNotSeekable()
    {
        $stream = new Stream();
        $stream->close();
        $this->assertFalse($stream->isSeekable());
    }

    public function testDetach()
    {
        $resource = fopen('php://temp', 'r');
        $stream = new Stream($resource);
        $this->assertSame(stream_get_meta_data($resource), stream_get_meta_data($stream->detach()));
        $this->assertNull($stream->getSize());
    }

    public function testEof()
    {
        $content = 'hello world';
        $stream = new Stream();
        $stream->write($content);
        $stream->rewind();
        $counter = 0;
        while (!$stream->eof() || $counter < 15) {
            $stream->read(1);
            $counter += 1;
        }

        $this->assertTrue($stream->eof());
    }

    public function testNotEof()
    {
        $content = 'hello world';
        $stream = new Stream();
        $stream->write($content);
        $stream->rewind();
        $stream->seek(2);
        $this->assertFalse($stream->eof());
    }

    public function testIsReadable()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream(fopen($file, 'r'));
        $this->assertTrue($stream->isReadable());
    }

    public function testIsNotReadable()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream(fopen($file, 'w'));
        $this->assertFalse($stream->isReadable());
    }

    public function test__toString()
    {
        $content = 'hello world';
        $stream = new Stream();
        $stream->write($content);
        $this->assertEquals($content, (string)$stream);
    }

    public function test__toStringThrowExceptionIfNotReadable(): void
    {
        $stream = new Stream(fopen('php://output', 'w'));
        $this->expectException(RuntimeException::class);
        $stream->__toString();
    }

    public function testTell()
    {
        $stream = new Stream();
        $stream->write('hello world');
        $stream->rewind();
        $stream->read(1);
        $stream->read(1);
        $stream->read(1);
        $this->assertEquals(3, $stream->tell());
    }

    public function testTellThrowsExceptionIfNoResource()
    {
        $stream = new Stream();
        $stream->close();
        $this->expectException(RuntimeException::class);
        $stream->tell();
    }

    public function testWrite()
    {
        $content = 'hello world';
        $stream = new Stream();
        $stream->write($content);
        $stream->rewind();
        $this->assertEquals($content, $stream->getContents());
    }

    public function testWriteThrowsExceptionIfNotWritable()
    {
        $stream = new Stream(fopen('php://temp', 'r'));
        $this->expectException(RuntimeException::class);
        $stream->write('hello world');
    }

    public function testWriteThrowsExceptionIfNoResource()
    {
        $stream = new Stream();
        $stream->close();
        $this->expectException(RuntimeException::class);
        $stream->write('hello world');
    }

    public function testRead()
    {
        $content = 'hello world';
        $stream = new Stream();
        $stream->write($content);
        $stream->rewind();
        $this->assertEquals($content, $stream->read(strlen($content)));
    }

    public function testReadThrowsExceptionIfNotReadable()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream(fopen($file, 'w'));
        $this->expectException(RuntimeException::class);
        $stream->read(3);
    }

    public function testReadThrowsExceptionIfNoResource()
    {
        $stream = new Stream();
        $stream->close();
        $this->expectException(RuntimeException::class);
        $stream->read(4);
    }

    public function testSeek()
    {
        $stream = new Stream();
        $stream->write('hello world');
        $stream->seek(4);
        $this->assertEquals('o', $stream->read(1));
        $stream->seek(4);
        $this->assertNotEquals('z', $stream->read(1));
    }

    public function testSeekThrowExceptionIfNoResource(): void
    {
        $stream = new Stream();
        $stream->close();
        $this->expectException(RuntimeException::class);
        $stream->seek(1);
    }

    public function testGetSize()
    {
        $stream = new Stream();
        $this->assertEquals(0, $stream->getSize());
        $stream->write('hello world');
        $this->assertEquals(11, $stream->getSize());
        $stream->close();
        $this->assertNull($stream->getSize());
    }

    public function testGetContents()
    {
        $content = 'hello world';
        $stream = new Stream();
        $stream->write($content);
        $stream->rewind();
        $this->assertEquals($content, $stream->getContents());
    }

    public function testIsWritable()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream(fopen($file, 'w'));
        $this->assertTrue($stream->isWritable());
    }

    public function testIsNotWritable()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream(fopen($file, 'r'));
        $this->assertFalse($stream->isWritable());
    }

    public function testGetMetadata()
    {
        $stream = new Stream();
        $this->assertEquals('PHP', $stream->getMetadata('wrapper_type'));
        $this->assertEquals('TEMP', $stream->getMetadata('stream_type'));
        $this->assertEquals('w+b', $stream->getMetadata('mode'));
        $this->assertEquals(0, $stream->getMetadata('unread_bytes'));
        $this->assertEquals(true, $stream->getMetadata('seekable'));
        $this->assertEquals('php://temp', $stream->getMetadata('uri'));
        $this->assertEquals(null, $stream->getMetadata('not-exist'));
        $this->assertIsArray($stream->getMetadata());
    }

    public function testClose()
    {
        $stream = new Stream();
        $stream->close();
        $this->assertNull($stream->getSize());
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $this->assertFalse($stream->isSeekable());
    }

    public function testRewind()
    {
        $content = 'hello world';
        $stream = new Stream();
        $stream->write($content);
        $stream->seek(0);
        $stream->read(3);
        $this->assertEquals(3, $stream->tell());
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('h', $stream->read(1));
    }
}
