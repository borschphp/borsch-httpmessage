<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 */
class Uri implements UriInterface
{
    protected string $scheme = '';

    protected string $userInfo = '';

    protected string $host = '';

    protected ?int $port = null;

    protected string $path = '';

    protected string $query = '';

    protected string $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            $parts = parse_url($uri);
            if ($parts === false) {
                throw new InvalidArgumentException("Unable to parse URI: $uri");
            }

            $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
            $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
            $this->host = isset($parts['host']) ? $parts['host'] : '';

            if (isset($parts['port'])) {
                if (!is_numeric($parts['port']) || $parts['port'] < 1 || $parts['port'] > 65535) {
                    throw new InvalidArgumentException("Invalid port value: {$parts['port']}");
                }
                $this->port = (int)$parts['port'];
            } else {
                $this->port = null;
            }

            $this->path = isset($parts['path']) ? $parts['path'] : '';
            $this->query = isset($parts['query']) ? $parts['query'] : '';
            $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
        }
    }

    public function __toString(): string
    {
        $uri = '';
        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }
        if ($this->getAuthority() !== '') {
            $uri .= '//' . $this->getAuthority();
        }
        $uri .= $this->path;
        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }
        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }
        return $uri;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function withScheme($scheme): static
    {
        if (!is_string($scheme)) {
            throw new InvalidArgumentException('Scheme must be a string');
        }

        $new = clone $this;
        $new->scheme = $scheme;
        return $new;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function withUserInfo($user, $password = null): static
    {
        if (!is_string($user)) {
            throw new InvalidArgumentException('User must be a string');
        }

        if (!is_string($password) && !is_null($password)) {
            throw new InvalidArgumentException('Password must be a string');
        }

        $new = clone $this;
        $new->userInfo = $user . ($password ? ':' . $password : '');
        return $new;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function withHost($host): static
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException('Host must be a string');
        }

        $new = clone $this;
        $new->host = $host;
        return $new;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function withPort($port): static
    {
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new \InvalidArgumentException("Invalid port: $port");
        }

        $new = clone $this;
        $new->port = $port;
        return $new;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function withPath($path): static
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('Path must be a string');
        }

        $new = clone $this;
        $new->path = $path;
        return $new;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function withQuery($query): static
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException('Query must be a string');
        }

        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withFragment($fragment): static
    {
        if (!is_string($fragment)) {
            throw new InvalidArgumentException('Fragment must be a string');
        }

        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
    }
}
