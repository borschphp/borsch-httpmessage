<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Borsch\Http\Exception\InvalidArgumentException;
use Psr\Http\Message\{RequestInterface, StreamInterface, UriInterface};

/**
 * Class Request
 */
class Request extends Message implements RequestInterface
{

    protected string $request_target;

    public function __construct(
        protected string $method,
        protected string|UriInterface $uri,
        string $protocol = '1.1',
        StreamInterface $body = null,
        array $headers = []
    ) {
        parent::__construct($protocol, $body, $headers);

        if (!$this->uri instanceof UriInterface) {
            $this->uri = new Uri($uri);
        }

        $this->request_target = $this->uri->getPath();
        if ($this->uri->getQuery()) {
            $this->request_target = "$this->request_target?{$this->uri->getQuery()}";
        }
    }


    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): static
    {
        if ($this->method === $method) {
            return $this;
        }

        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getRequestTarget(): string
    {
        return $this->request_target;
    }

    public function withRequestTarget(string $request_target): static
    {
        if ($this->request_target === $request_target) {
            return $this;
        }

        $new = clone $this;
        $new->request_target = $request_target;
        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserve_host = false): static
    {
        if ($this->uri === $uri) {
            return $this;
        }

        $new = clone $this;
        $new->uri = $uri;

        if (!$preserve_host) {
            return $new;
        }

        if (!$new->hasHeader('host')) {
            throw InvalidArgumentException::notFound('host header');
        }

        $host = $new->getHeaderLine('host');

        return $new->withoutHeader('host')->withUri($uri->withHost($host));
    }
}
