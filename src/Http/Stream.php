<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Borsch\Http\Exception\RuntimeException;
use Exception;
use Psr\Http\Message\StreamInterface;
use function fopen, fclose, clearstatcache, fstat, ftell, stream_get_meta_data, fread, strpbrk, fseek, feof, stream_get_contents, fwrite;

/**
 * Class Stream
 */
class Stream implements StreamInterface
{

    protected $resource;

    protected ?int $size = null;
    protected array $metadata;
    protected bool $seekable;
    protected bool $readable;
    protected bool $writable;

    public function __construct(string $resource = 'php://memory', string $mode = 'r+')
    {
        $this->resource = fopen($resource, $mode);
        $this->size = $this->getSize();

        $this->metadata = stream_get_meta_data($this->resource);
        $this->seekable = $this->metadata['seekable'] ?? false;
        $this->writable = strpbrk($this->metadata['mode'] ?? '', 'waxc+') !== false;
        $this->readable = strpbrk($this->metadata['mode'] ?? '', 'r+') !== false;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function __toString(): string
    {
        try {
            $this->seek(0);
            return $this->getContents();
        } catch (Exception) {
            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->resource)) {
            fclose($this->resource);
            $this->detach();
        }
    }

    public function detach()
    {
        $result = $this->resource;
        unset($this->resource);
        $this->size = null;
        $this->seekable = false;
        $this->readable = false;
        $this->writable = false;
        $this->metadata = [];

        return $result;
    }

    public function getSize(): ?int
    {
        if ($this->size !== null || !isset($this->resource)) {
            return $this->size;
        }

        // Clear the stat cache if the stream has a URI
        if ($uri = $this->getMetadata('uri')) {
            clearstatcache(true, $uri);
        }

        $stats = fstat($this->resource);
        if (isset($stats['size'])) {
            return $this->size ??= $stats['size'];
        }

        return null;
    }

    public function tell(): int
    {
        $position = ftell($this->resource);
        if ($position === false) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }

        return $position;
    }


    public function getMetadata(?string $key = null)
    {
        if (!$key) {
            return $this->metadata;
        }

        return $this->metadata[$key] ?? null;
    }

    public function read(int $length): string
    {
        if (!$this->readable) {
            throw RuntimeException::streamIsNotReadable();
        }

        $data = fread($this->resource, $length);
        if ($data === false) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }

        return $data;
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->seekable) {
            throw RuntimeException::streamIsNotSeekable();
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }
    }

    public function rewind(): void
    {
        if (!$this->seekable) {
            throw RuntimeException::streamIsNotSeekable();
        }

        $this->seek(0);
    }

    public function getContents(): string
    {
        if (!$this->readable) {
            throw RuntimeException::streamIsNotReadable();
        }

        $contents = stream_get_contents($this->resource);
        if ($contents === false) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }

        return $contents;
    }


    public function isReadable(): bool
    {
        return $this->readable;
    }


    public function write(string $string): int
    {
        if (!$this->writable) {
            throw RuntimeException::streamIsNotWritable();
        }

        $bytes = fwrite($this->resource, $string);
        if ($bytes === false) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }

        $this->size = null;

        return $bytes;
    }

    public function eof(): bool
    {
        return feof($this->resource);
    }
}
