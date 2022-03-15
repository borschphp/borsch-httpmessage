<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class Message
 */
class Message implements MessageInterface
{

    /** @var string[] */
    protected static $supported_protocol_versions = ['1.0', '1.1', '2.0', '2'];

    /** @var string */
    protected $protocol_version = '1.1';

    /** @var string[][] */
    protected $headers = [];

    /** @var string[] */
    protected $header_names = [];

    /** @var StreamInterface */
    protected $stream;

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol_version;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version): MessageInterface
    {
        if ($this->protocol_version === $version) {
            return $this;
        }

        if (!is_string($version)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string, got a %s.',
                is_object($version) ? get_class($version) : gettype($version)
            ));
        }

        if (!in_array($version, self::$supported_protocol_versions)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown protocol version, expected one of: %s.',
                implode(', ', self::$supported_protocol_versions)
            ));
        }

        $new = clone $this;
        $new->protocol_version = $version;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name): bool
    {
        return is_string($name) && isset($this->header_names[strtolower($name)]);
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name): array
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        return $this->headers[$this->header_names[strtolower($name)]];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name): string
    {
        $header = $this->getHeader($name);
        if (!count($header)) {
            return '';
        }

        return implode(', ', $header);
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $normalized = $this->normalizeHeaderName($name);
        $value = $this->normalizeHeaderValue($value);

        $new = clone $this;
        if (isset($new->header_names[$normalized])) {
            unset($new->headers[$new->header_names[$normalized]]);
        }

        $new->header_names[$normalized] = $name;
        $new->headers[$name] = $value;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value): MessageInterface
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $header = $this->header_names[$this->normalizeHeaderName($name)];
        $value = $this->normalizeHeaderValue($value);

        $new = clone $this;
        $new->headers[$header] = array_merge($this->headers[$header], $value);

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name): MessageInterface
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $normalized = $this->normalizeHeaderName($name);
        $new = clone $this;

        unset(
            $new->headers[$this->header_names[$normalized]],
            $new->header_names[$normalized]
        );

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = new Stream();
        }

        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($this->stream === $body) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;

        return $new;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function normalizeHeaderName(string $name): string
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf(
                '`%s` is not valid header name.',
                is_object($name) ? get_class($name) : gettype($name)
            ));
        }

        return strtolower($name);
    }

    /**
     * @param string|string[] $value
     * @return string[]
     */
    protected function normalizeHeaderValue($value): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        if (!count($value)) {
            throw new InvalidArgumentException(
                'Header value must be a string or an array of strings, empty array given.'
            );
        }

        foreach ($value as $val) {
            if (!is_string($val) && !is_numeric($val)) {
                throw new InvalidArgumentException(sprintf(
                    '"%s" is not valid header value.',
                    (is_object($val) ? get_class($val) : gettype($val))
                ));
            }
        }

        return $value;
    }
}
