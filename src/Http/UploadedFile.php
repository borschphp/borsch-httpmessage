<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * Class UploadedFile
 */
class UploadedFile implements UploadedFileInterface
{

    protected StreamInterface $stream;

    protected string $clientFilename;

    protected string $clientMediaType;

    protected int $error;

    protected int $size;

    protected bool $has_been_moved = false;

    public function __construct(
        StreamInterface $stream,
        int $size,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ) {
        $this->stream = $stream;
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    public function getStream(): StreamInterface
    {
        if ($this->stream->getSize() === null) {
            throw new RuntimeException('Stream is missing, it probably has been moved already');
        }

        return $this->stream;
    }

    public function moveTo($targetPath): void
    {
        if ($this->has_been_moved) {
            throw new RuntimeException('Uploaded file has already been moved');
        }

        if (!is_writable(dirname($targetPath))) {
            throw new RuntimeException('Upload directory is not writable');
        }

        $this->stream->rewind();
        $target = fopen($targetPath, 'wb');

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
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
