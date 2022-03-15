<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 */
class Request extends Message implements RequestInterface
{

    /** @var string */
    protected $method = 'GET';

    /** @var string|null */
    protected $request_target;

    /** @var UriInterface */
    protected $uri;

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        if (!$this->request_target) {
            $target = $this->uri->getPath();
            $query = $this->uri->getQuery();

            if ($target !== '' && $query !== '') {
                $target .= '?' . $query;
            }

            $this->request_target = $target ?: '/';
        }

        return $this->request_target;
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget($requestTarget): RequestInterface
    {
        if ($this->request_target == $requestTarget) {
            return $this;
        }

        if (!is_string($requestTarget)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid request target. Expected a string without whitespace, got "%s".',
                is_object($requestTarget) ? get_class($requestTarget) : gettype($requestTarget)
            ));
        }

        $new = clone $this;
        $new->request_target = $requestTarget;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method): RequestInterface
    {
        if ($this->method == $method) {
            return $this;
        }

        if (!is_string($method)) {
            throw new InvalidArgumentException(sprintf(
                'Method must be a string, %s received.',
                is_object($method) ? get_class($method) : gettype($method)
            ));
        }

        $new = clone $this;
        $new->method = strtoupper($method);

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost || !$new->hasHeader('host')) {
            $host = $new->uri->getHost();
            if ($host != '') {
                $port = $new->uri->getPort();
                if ($port) {
                    $host .= ':'.$port;
                }

                $new->header_names['host'] = $new->header_names['host'] ?? 'Host';
                $new->headers = [$this->header_names['host'] => [$host]] + $this->headers;
            }
        }

        return $new;
    }
}
