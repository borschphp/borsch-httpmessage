<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Borsch\Http\Exception\RuntimeException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFile
 */
class UploadedFile implements UploadedFileInterface
{

    protected bool $has_been_moved = false;

    public function __construct(
        protected StreamInterface $stream,
        protected int $size,
        protected int $error = UPLOAD_ERR_OK,
        protected ?string $client_filename = null,
        protected ?string $client_media_type = null
    ) {}

    public function getStream(): StreamInterface
    {
        if ($this->stream->getSize() === null) {
            throw RuntimeException::streamMissingHasBeenMoved();
        }

        return $this->stream;
    }

    public function moveTo(string $target_path): void
    {
        if ($this->has_been_moved) {
            throw RuntimeException::uploadedFileAlreadyMoved();
        }

        if (!is_writable(dirname($target_path))) {
            throw RuntimeException::uploadDirIsNotWritable();
        }

        $this->stream->rewind();
        $target = fopen($target_path, 'wb');

        while (!$this->stream->eof()) {
            fwrite($target, $this->stream->read(4096));
        }

        fclose($target);
        $this->stream->close();

        $this->has_been_moved = true;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->client_filename;
    }

    public function getClientMediatype(): ?string
    {
        return $this->client_media_type;
    }
}
