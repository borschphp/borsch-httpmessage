<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Psr\Http\Message\{RequestInterface, StreamInterface, UriInterface};

/**
 * Class Request
 */
class Request extends Message implements RequestInterface
{

    protected string $request_target;

    public function __construct(
        protected string $method,
        protected UriInterface $uri,
        string $protocol = '1.1',
        StreamInterface $body = null,
        array $headers = []
    ) {
        parent::__construct($protocol, $body, $headers);

        $this->request_target = $this->uri->getPath();
        if ($this->uri->getQuery()) {
            $this->request_target .= '?' . $uri->getQuery();
        }
    }


    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod($method): static
    {
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getRequestTarget(): string
    {
        return $this->request_target;
    }

    public function withRequestTarget($requestTarget): static
    {
        $new = clone $this;
        $new->request_target = $requestTarget;
        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): static
    {
        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost) {
            return $new;
        }

        if (!$new->hasHeader('host')) {
            throw new \InvalidArgumentException('host header not found');
        }

        $host = $new->getHeaderLine('host');
        $new = $new->withoutHeader('host');
        $new = $new->withUri($uri->withHost($host));

        return $new;
    }
}
