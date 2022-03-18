<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Class Stream
 */
class Stream implements StreamInterface
{

    /** @var resource|null */
    protected $resource;

    /** @var int */
    protected $size;

    /** @var bool */
    protected $seekable;

    /** @var bool */
    protected $writable;

    /** @var bool */
    protected $readable;

    /**
     * @param resource|null $resource
     */
    public function __construct($resource = null)
    {
        if ($resource !== null && !is_resource($resource)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid resource provided, expected null or resource but got %s instead',
                is_object($resource) ? get_class($resource) : gettype($resource)
            ));
        }

        $stream = $resource ?: fopen('php://temp', 'wb+');
        if ($stream === false) {
            throw new RuntimeException('Unable to open stream or file.');
        }

        $this->resource = $stream;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->isSeekable()) {
            $this->rewind();
        }

        return $this->getContents();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->resource) {
            $resource = $this->detach();
            fclose($resource);
        }
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;
        $this->size = null;
        $this->seekable = false;
        $this->writable = false;
        $this->readable = false;

        return $resource;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        if (!$this->resource) {
            return null;
        }

        if (!$this->size) {
            $stats = fstat($this->resource);
            $this->size = $stats['size'] ?? null;
        }

        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource, unable to tell position.');
        }

        $result = ftell($this->resource);
        if ($result === false) {
            throw new RuntimeException('An error occurred.');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        return !$this->resource || feof($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable(): bool
    {
        if (!$this->seekable) {
            $this->seekable = $this->resource && $this->getMetadata('seekable');
        }

        return $this->seekable;
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available, unable to seek position.');
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable.');
        }

        if (fseek($this->resource, $offset, $whence) !== 0) {
            throw new RuntimeException('Error seeking within stream.');
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        if (!$this->writable) {
            $mode = $this->getMetadata('mode');

            $this->writable = is_string($mode) && array_reduce(str_split($mode), function ($carry, $char) {
                if (!$carry && in_array($char, ['w', '+', 'x', 'c', 'a'])) {
                    $carry = true;
                }

                return $carry;
            }, false);
        }

        return $this->writable;
    }

    /**
     * @inheritDoc
     */
    public function write($string): int
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available, unable to write.');
        }

        if (!$this->isWritable()) {
            throw new RuntimeException('Stream is not writable.');
        }

        $this->size = null;

        $result = fwrite($this->resource, $string);
        if ($result === false) {
            throw new RuntimeException('Error writing to stream.');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isReadable(): bool
    {
        if (!$this->readable) {
            $mode = $this->getMetadata('mode');

            $this->readable = is_string($mode) && (
                    strpos($mode, 'r') !== false ||
                    strpos($mode, '+') !== false
                );
        }

        return $this->readable;
    }

    /**
     * @inheritDoc
     */
    public function read($length): string
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available, unable to read.');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable.');
        }

        $result = fread($this->resource, $length);
        if ($result === false) {
            throw new RuntimeException('Error reading stream.');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable.');
        }

        $result = stream_get_contents($this->resource);
        if ($result === false) {
            throw new RuntimeException('Error reading stream.');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        if (!$this->resource) {
            return $key ? null : [];
        }

        $metadata = stream_get_meta_data($this->resource);
        if ($key === null) {
            return $metadata;
        }

        return $metadata[$key] ?? null;
    }
}
