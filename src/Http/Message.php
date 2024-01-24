<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Psr\Http\Message\{MessageInterface, StreamInterface};
use function array_merge, strtolower, implode;

/**
 * Class Message
 */
class Message implements MessageInterface
{

    /** @var Header[] $headers */
    protected array $headers = [];


    public function __construct(
        protected string $protocol = '1.1',
        protected ?StreamInterface $body = null,
        array $headers = []
    ) {
        $this->body = $body ?? new Stream();
        foreach ($headers as $name => $values) {
            $this->headers[strtolower($name)] = new Header($name, $values);
        }
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
        $headers = [];
        foreach ($this->headers as $header) {
            $headers[$header->name] = $header->values;
        }
        return $headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)]->values ?? [];
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
        $new = clone $this;

        $name_lower = strtolower($name);
        unset($new->headers[$name_lower]);

        $new->headers[$name_lower] = new Header($name, $value);

        return $new;
    }

    public function withAddedHeader(string $name, $value): static
    {
        $name_lower = strtolower($name);

        $new = clone $this;

        if ($new->hasHeader($name)) {
            $new->headers[$name_lower] = new Header(
                $new->headers[$name_lower]->name,
                array_merge($new->headers[$name_lower]->values, (array)$value)
            );
        } else {
            $new->headers[$name_lower] = new Header($name, $value);
        }

        return $new;
    }

    public function withoutHeader(string $name): static
    {
        $name_lower = strtolower($name);
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $new = clone $this;
        unset($new->headers[$name_lower]);

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
