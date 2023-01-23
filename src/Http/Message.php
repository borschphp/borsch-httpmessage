<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use InvalidArgumentException;
use Psr\Http\Message\{MessageInterface, StreamInterface};

/**
 * Class Message
 */
class Message implements MessageInterface
{

    protected array $headers_lowercase = [];

    public function __construct(
        protected string $protocol = '1.1',
        protected ?StreamInterface $body = null,
        protected array $headers = []
    ) {
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

    public function withProtocolVersion($version): static
    {
        if (!is_string($version) || !preg_match('/^\d+(\.\d+)?$/', $version)) {
            throw new InvalidArgumentException('Invalid protocol version');
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Header name must be a string');
        }

        return isset($this->headers_lowercase[strtolower($name)]);
    }

    public function getHeader($name): array
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Header name must be a string');
        }

        $name_lower = strtolower($name);
        if (!$this->hasHeader($name_lower)) {
            return [];
        }

        return $this->headers[$this->headers_lowercase[$name_lower]] ?? [];
    }

    public function getHeaderLine($name): string
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Header name must be a string');
        }

        if (!$this->hasHeader($name)) {
            return '';
        }

        return implode(',', $this->getHeader($name));
    }

    public function withHeader($name, $value): static
    {
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException('Header name must be a string');
        }

        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException('Header value must be a string or an array of strings');
        }

        if (is_array($value)) {
            foreach($value as $header) {
                if(!is_string($header)) {
                    throw new InvalidArgumentException('Header value must be a string or an array of strings');
                }
            }
        }

        $name_lower = strtolower($name);

        $new = clone $this;
        $new->headers_lowercase[$name_lower] = $name;
        $new->headers[$name] = (array)$value;

        return $new;
    }

    public function withAddedHeader($name, $value): static
    {
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException('Invalid header name');
        }

        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException('Invalid header value');
        }

        if (is_array($value)) {
            foreach ($value as $header) {
                if (!is_string($header)) {
                    throw new InvalidArgumentException('Header value must be a string or an array of strings');
                }
            }
        }
        
        $name_lower = strtolower($name);

        $new = clone $this;
        if (isset($new->headers_lowercase[$name_lower])) {
            $new->headers[$new->headers_lowercase[$name_lower]] = array_merge(
                $new->headers[$new->headers_lowercase[$name_lower]],
                (array)$value
            );
        } else {
            $new->headers_lowercase[$name_lower] = $name;
            $new->headers[$name] = (array)$value;
        }

        return $new;
    }

    public function withoutHeader($name): static
    {
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException('Invalid header name');
        }

        $name_lower = strtolower($name);

        $new = clone $this;
        if (!isset($new->headers_lowercase[$name_lower])) {
            return $new;
        }

        foreach (array_keys($this->headers) as $key) {
            if (strtolower($key) == $name_lower) {
                unset($new->headers[$key]);
            }
        }
        unset($new->headers_lowercase[$name_lower]);

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
