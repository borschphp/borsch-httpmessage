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

        if (!$this->hasHeader('Host')) {
            $this->headers[] = new Header('Host', $this->uri->getHost());
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

        if (!strlen($method)) {
            throw InvalidArgumentException::mustBeAString('Method');
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

        $request_target = $new->uri->getPath();
        if ($new->uri->getQuery()) {
            $request_target = "$request_target?{$new->uri->getQuery()}";
        }

        $new = $new->withRequestTarget($request_target);

        if ($preserve_host) {
            $host_header = $this->getHeaderLine('Host');
            $new_host_header = $new->uri->getHost();

            if (!strlen($host_header) && strlen($new_host_header)) {
                return $new->withHeader('Host', $new->uri->getHost());
            } elseif (!strlen($host_header) && !strlen($new_host_header) || strlen($host_header)) {
                return $new;
            }
        }

        if ($new->uri->getHost()) {
            return $new->withHeader('Host', $new->uri->getHost());
        }

        return $new;
    }
}
