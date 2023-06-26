<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Borsch\Http\Exception\InvalidArgumentException;
use Psr\Http\Message\{MessageInterface, StreamInterface};

/**
 * Class Message
 */
class Message implements MessageInterface
{

    protected array $headers_lowercase = [];

    public function __construct(
        protected string           $protocol = '1.1',
        protected ?StreamInterface $body = null,
        protected array            $headers = []
    )
    {
        $this->body = $body ?? new Stream('php://temp', 'r+');
        $this->headers_lowercase = array_combine(
            array_keys(array_change_key_case($headers)),
            array_keys($this->headers)
        );
    }

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion(string $version): static
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers_lowercase[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        $name_lower = strtolower($name);
        if (!$this->hasHeader($name_lower)) {
            return [];
        }

        return $this->headers[$this->headers_lowercase[$name_lower]] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        if (!$this->hasHeader($name)) {
            return '';
        }

        return implode(',', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): static
    {
        if (empty($name)) {
            throw InvalidArgumentException::mustBeAString('Header name');
        }

        if (!is_string($value) && !is_array($value)) {
            throw InvalidArgumentException::mustBeAStringOrAnArrayOfString('Header value');
        }

        foreach ((array)$value as $header) {
            if (!is_string($header)) {
                throw InvalidArgumentException::mustBeAStringOrAnArrayOfString('Header value');
            }
        }

        $name_lower = strtolower($name);

        $new = clone $this;
        $new->headers_lowercase[$name_lower] = $name;
        $new->headers[$name] = (array)$value;

        return $new;
    }

    public function withAddedHeader(string $name, $value): static
    {
        if (empty($name)) {
            throw InvalidArgumentException::invalid('header name');
        }

        if (!is_string($value) && !is_array($value)) {
            throw InvalidArgumentException::invalid('header value');
        }

        foreach ((array)$value as $header) {
            if (!is_string($header)) {
                throw InvalidArgumentException::mustBeAStringOrAnArrayOfString('Header value');
            }
        }

        $name_lower = strtolower($name);

        $new = clone $this;
        $new->headers_lowercase[$name_lower] = $name;
        $new->headers[$name] = array_merge(
            $new->headers[$name] ?? [],
            (array)$value
        );

        return $new;
    }

    public function withoutHeader(string $name): static
    {
        if (empty($name)) {
            throw InvalidArgumentException::invalid('header name');
        }

        $name_lower = strtolower($name);
        if (!isset($this->headers_lowercase[$name_lower])) {
            return $this;
        }

        $new = clone $this;

        unset(
            $new->headers[$new->headers_lowercase[$name_lower]],
            $new->headers_lowercase[$name_lower]
        );

        return $new;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): static
    {
        $new = clone $this;
        $new->body = $body;

        return $new;
    }
}
