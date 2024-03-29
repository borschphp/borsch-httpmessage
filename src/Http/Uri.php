<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Borsch\Http\Exception\InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use function parse_url, is_numeric, strtr;
use function RingCentral\Psr7\str;

/**
 * Class Uri
 */
class Uri implements UriInterface
{

    protected const SCHEMES = [80 => 'http', 443 => 'https'];

    protected string $scheme = '';
    protected string $user_info = '';
    protected string $host = '';
    protected ?int $port = null;
    protected string $path = '';
    protected string $query = '';
    protected string $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            $parts = parse_url(strtolower($uri));
            if ($parts === false) {
                throw InvalidArgumentException::unableToParseUri($uri);
            }

            $this->scheme = $parts['scheme'] ?? '';
            $this->user_info = $parts['user'] ?? '';
            if (isset($parts['pass'])) {
                $this->user_info .= ':'.$parts['pass'];
            }
            $this->host = $parts['host'] ?? '';

            if (isset($parts['port'])) {
                if (!is_numeric($parts['port']) || $parts['port'] < 1 || $parts['port'] > 65535) {
                    throw InvalidArgumentException::invalid('port value: '.$parts['port']);
                }

                $port = (int)$parts['port'];
                if ((static::SCHEMES[$port] ?? -1) !== $this->scheme) {
                    $this->port = (int)$parts['port'];
                }
            }

            $this->path = str_replace(' ', '%20', $parts['path'] ?? '');
            $this->query = $parts['query'] ?? '';
            $this->fragment = $parts['fragment'] ?? '';
        }
    }

    public function __toString(): string
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri = "$this->scheme:";
        }

        if ($this->getAuthority() !== '') {
            $uri = "$uri//{$this->getAuthority()}";
        }

        $uri = "$uri$this->path";
        if ($this->query !== '') {
            $uri = "$uri?$this->query";
        }

        if ($this->fragment !== '') {
            $uri = "$uri#$this->fragment";
        }

        return $uri;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function withScheme(string $scheme): static
    {
        if (is_numeric($scheme)) {
            throw InvalidArgumentException::mustBeAString('Scheme');
        }

        if ($this->scheme === $scheme) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;

        return $new;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;

        if ($this->user_info !== '') {
            $authority = "$this->user_info@$authority";
        }

        if ($this->port !== null && (static::SCHEMES[$this->port] ?? -1) != $this->scheme) {
            $authority = "$authority:$this->port";
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->user_info;
    }

    public function withUserInfo(string $user, ?string $password = null): static
    {
        $new = clone $this;
        $new->user_info = str_replace(
            ['@', '#'],
            ['%40', '%23'],
            $user.($password !== null ? ":$password" : ''));

        return $new;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function withHost(string $host): static
    {
        if ($this->host === $host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function withPort(?int $port): static
    {
        if ($this->port === $port) {
            return $this;
        }

        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw InvalidArgumentException::invalid('port: '.$port);
        }

        $new = clone $this;
        $new->port = (int)$port;

        return $new;
    }

    public function getPath(): string
    {
        if (str_starts_with($this->path, '//')) {
            return '/'.ltrim($this->path, '/');
        }

        return $this->path;
    }

    public function withPath(string $path): static
    {
        if ($this->path === $path) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function withQuery(string $query): static
    {
        if ($this->query === $query) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withFragment(string $fragment): static
    {
        if ($this->fragment === $fragment) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }
}
