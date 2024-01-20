<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Psr\Http\Message\{MessageInterface, StreamInterface};
use function array_merge, strtolower, implode, array_reduce;

/**
 * Class Message
 */
class Message implements MessageInterface
{

    /** @var Header[] $headers */
    protected array $headers = [];


    public function __construct(
        protected string           $protocol = '1.1',
        protected ?StreamInterface $body = null,
        array $headers = []
    )
    {
        $this->body = $body ?? new Stream('php://temp', 'r+');
        foreach ($headers as $name => $values) {
            $this->headers[] = new Header($name, $values);
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
        $name_lower = strtolower($name);

        return array_reduce(
            $this->headers,
            fn ($has, Header $header) => $has || $header->normalized_name == $name_lower,
            false
        );
    }

    public function getHeader(string $name): array
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        $name_lower = strtolower($name);

        return array_reduce(
            $this->headers,
            fn ($header, Header $current) => $current->normalized_name == $name_lower ?
                $current->values : $header,
            []
        );
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
        $new->headers[] = new Header($name, $value);

        return $new;
    }

    public function withAddedHeader(string $name, $value): static
    {
        $name_lower = strtolower($name);

        $new = clone $this;

        if ($new->hasHeader($name)) {
            foreach ($new->headers as $index => $header) {
                if ($header->normalized_name == $name_lower) {
                    $new->headers[$index] = new Header(
                        $header->name,
                        array_merge($header->values, (array)$value)
                    );
                    break;
                }
            }
        } else {
            $new->headers[] = new Header($name, $value);
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
        foreach ($new->headers as $index => $header) {
            if ($header->normalized_name == $name_lower) {
                unset($new->headers[$index]);
                break;
            }
        }

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
