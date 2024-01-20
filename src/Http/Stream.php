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

    public function __construct(string $resource = 'php://memory', string $mode = 'r+')
    {
        $this->resource = fopen($resource, $mode);
        $this->size = $this->getSize();
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
        if (!isset($this->resource)) {
            return null;
        }

        $result = $this->resource;
        unset($this->resource);
        $this->size = null;

        return $result;
    }

    public function getSize(): ?int
    {
        if (!isset($this->resource)) {
            return null;
        }

        // Clear the stat cache if the stream has a URI
        if ($uri = $this->getMetadata('uri')) {
            clearstatcache(true, $uri);
        }

        $stats = fstat($this->resource);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    public function tell(): int
    {
        if (!isset($this->resource)) {
            throw RuntimeException::noResourceAvailableCantDoAction(__METHOD__);
        }

        $position = ftell($this->resource);
        if ($position === false) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }

        return $position;
    }


    public function getMetadata(?string $key = null)
    {
        if (!isset($this->resource)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->resource);

        if (!$key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function read(int $length): string
    {
        if (!$this->isReadable()) {
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
        if (!isset($this->resource)) {
            return false;
        }

        return $this->getMetadata('seekable');
    }

    public function isWritable(): bool
    {
        if (!isset($this->resource)) {
            return false;
        }

        return strpbrk($this->getMetadata('mode'), 'waxc+') !== false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->resource)) {
            throw RuntimeException::noResourceAvailableCantDoAction(__METHOD__);
        }

        if (!$this->isSeekable()) {
            throw RuntimeException::streamIsNotSeekable();
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }
    }

    public function rewind(): void
    {
        if (!$this->isSeekable()) {
            throw RuntimeException::streamIsNotSeekable();
        }

        $this->seek(0);
    }

    public function getContents(): string
    {
        if (!isset($this->resource)) {
            throw RuntimeException::noResourceAvailableCantDoAction(__METHOD__);
        }

        if (!$this->isReadable()) {
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
        if (!isset($this->resource)) {
            return false;
        }

        return strpbrk($this->getMetadata('mode'), 'r+') !== false;
    }


    public function write(string $string): int
    {
        if (!isset($this->resource)) {
            throw RuntimeException::noResourceAvailableCantDoAction(__METHOD__);
        }

        if (!$this->isWritable()) {
            throw RuntimeException::streamIsNotWritable();
        }

        $bytes = fwrite($this->resource, $string);
        if ($bytes === false) {
            throw RuntimeException::errorOccurredDuringMethodCall(__METHOD__);
        }

        return $bytes;
    }

    public function eof(): bool
    {
        if (!isset($this->resource)) {
            return true;
        }

        return feof($this->resource);
    }
}
