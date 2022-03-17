<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\Stream;
use Borsch\Message\UploadedFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UploadedFileTest extends TestCase
{

    public function testMoveTo()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream();
        $stream->write('content');
        $uploaded = new UploadedFile($stream, 1024);
        $uploaded->moveTo($file);
        $this->assertFileExists($file);
        $this->assertEquals('content', file_get_contents($file));
        $this->assertEquals((string)$stream, file_get_contents($file));
    }

    public function testGetClientFilename()
    {
        $uploaded = new UploadedFile(new Stream(), 1024);
        $this->assertNull($uploaded->getClientFilename());
        $uploaded = new UploadedFile(new Stream(), 1024, UPLOAD_ERR_OK,'file.txt');
        $this->assertEquals('file.txt', $uploaded->getClientFilename());
    }

    public function testGetError()
    {
        $uploaded = new UploadedFile(new Stream(), 1024, UPLOAD_ERR_OK);
        $this->assertEquals(UPLOAD_ERR_OK, $uploaded->getError());
        $uploaded = new UploadedFile(new Stream(), 1024, UPLOAD_ERR_PARTIAL);
        $this->assertEquals(UPLOAD_ERR_PARTIAL, $uploaded->getError());
        $uploaded = new UploadedFile(new Stream(), 1024, UPLOAD_ERR_NO_TMP_DIR);
        $this->assertEquals(UPLOAD_ERR_NO_TMP_DIR, $uploaded->getError());
    }

    public function testGetSize()
    {
        $uploaded = new UploadedFile(new Stream(), 1024);
        $this->assertEquals(1024, $uploaded->getSize());
    }

    public function testGetStream()
    {
        $stream = new Stream();
        $uploaded = new UploadedFile($stream, 1024);
        $this->assertEquals($stream, $uploaded->getStream());
    }

    public function testGetStreamThrowsExceptionIfUploadError()
    {
        $uploaded = new UploadedFile(new Stream(), 1024, UPLOAD_ERR_NO_FILE);
        $this->expectException(RuntimeException::class);
        $uploaded->getStream();
    }

    public function testGetStreamThrowsExceptionIfAlreadyMoved()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream();
        $stream->write('content');
        $uploaded = new UploadedFile($stream, 1024);
        $uploaded->moveTo($file);
        $this->expectException(RuntimeException::class);
        $uploaded->getStream();
    }

    public function testGetClientMediaType()
    {
        $uploaded = new UploadedFile(new Stream(), 1024);
        $this->assertNull($uploaded->getClientMediaType());
        $uploaded = new UploadedFile(new Stream(), 1024, UPLOAD_ERR_OK,'file.json', 'application/json');
        $this->assertEquals('application/json', $uploaded->getClientMediaType());
    }
}
