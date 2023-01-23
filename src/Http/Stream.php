<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Exception;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

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
        } catch (Exception $e) {
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
            throw new RuntimeException('No resource available; cannot tell position');
        }

        $position = ftell($this->resource);
        if ($position === false) {
            throw new RuntimeException('Error occurred during tell operation');
        }

        return $position;
    }


    public function getMetadata($key = null)
    {
        if ($key !== null && !is_string($key)) {
            throw new InvalidArgumentException('Key must be a string');
        }

        if (!isset($this->resource)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->resource);

        if (!$key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function read($length): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        if (!is_int($length)) {
            throw new InvalidArgumentException('Length must be an integer');
        }

        $data = fread($this->resource, $length);
        if ($data === false) {
            throw new RuntimeException('Error occurred during read operation');
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

    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('No resource available; cannot seek');
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        if (!is_int($offset)) {
            throw new InvalidArgumentException('Offset must be an integer');
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new RuntimeException('Error occurred during seek operation');
        }
    }

    public function rewind(): void
    {
        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        $this->seek(0);
    }

    public function getContents(): string
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('No resource available; cannot read');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $contents = stream_get_contents($this->resource);
        if ($contents === false) {
            throw new RuntimeException('Error occurred during get contents operation');
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


    public function write($string): int
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('No resource available; cannot write');
        }

        if (!$this->isWritable()) {
            throw new RuntimeException('Stream is not writable');
        }

        if (!is_string($string)) {
            throw new InvalidArgumentException('Data must be a string');
        }

        $bytes = fwrite($this->resource, $string);
        if ($bytes === false) {
            throw new RuntimeException('Error occurred during write operation');
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
