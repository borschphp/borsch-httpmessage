<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\Stream;
use Borsch\Message\UploadedFile;
use Borsch\Message\UploadedFileFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileFactoryTest extends TestCase
{

    public function testCreateUploadedFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'borsch_');
        $stream = new Stream(fopen($file, 'wb+'));
        $uploaded = (new UploadedFileFactory())->createUploadedFile(
            $stream,
            1024,
            UPLOAD_ERR_OK,
            $clientFilename = 'file.txt',
            $clientMediaType = 'text/plain'
        );
        $this->assertInstanceOf(UploadedFile::class, $uploaded);
        $this->assertInstanceOf(UploadedFileInterface::class, $uploaded);
        $this->assertInstanceOf(StreamInterface::class, $uploaded->getStream());
        $this->assertInstanceOf(Stream::class, $uploaded->getStream());
        $this->assertEquals($stream, $uploaded->getStream());
        $this->assertEquals(1024, $uploaded->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $uploaded->getError());
        $this->assertEquals($clientFilename, $uploaded->getClientFilename());
        $this->assertEquals($clientMediaType, $uploaded->getClientMediaType());
    }
}
