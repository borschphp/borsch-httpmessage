<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 */
class Uri implements UriInterface
{

    /** @var string */
    protected $scheme = '';

    /** @var string */
    protected $user_info = '';

    /** @var string */
    protected $host = '';

    /** @var int|null */
    protected $port = null;

    /** @var string */
    protected $path = '';

    /** @var string */
    protected $query = '';

    /** @var string */
    protected $fragment = '';

    /** @var string|null */
    protected $composed_uri;

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        if (is_string($this->composed_uri)) {
            return $this->composed_uri;
        }

        $this->composed_uri = '';

        if ($this->scheme != '') {
            $this->composed_uri .= $this->scheme.':';
        }

        $authority = $this->getAuthority();
        if ($authority != '') {
            $this->composed_uri .= '//'.$authority;
        }

        if ($this->path != '') {
            $this->composed_uri .= $authority ? '/'.ltrim($this->path, '/') : $this->path;
        }

        if ($this->query != '') {
            $this->composed_uri .= '?'.$this->query;
        }

        if ($this->fragment != '') {
            $this->composed_uri .= '#'.$this->fragment;
        }

        return $this->composed_uri;
    }

    /**
     * Resets composed uri.
     */
    public function __clone()
    {
        $this->composed_uri = null;
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        $authority = $this->host;
        if ($authority == '') {
            return '';
        }

        if ($this->user_info !== '') {
            $authority = $this->user_info.'@'.$authority;
        }

        if ($this->isNonStandardPort()) {
            $authority .= ':'.$this->port;
        }

        return $authority;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return $this->user_info;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        return $this->isNonStandardPort() ? $this->port : null;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function withScheme($scheme): UriInterface
    {
        if (!is_string($scheme)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string but got %s.',
                is_object($scheme) ? get_class($scheme) : gettype($scheme)
            ));
        }

        $scheme = strtolower($scheme);
        if ($this->scheme == $scheme) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo($user, $password = null): UriInterface
    {
        if ($this->user_info == $user) {
            return $this;
        }

        if (!is_string($user)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string but got %s.',
                is_object($user) ? get_class($user) : gettype($user)
            ));
        }

        if ($password && !is_string($password)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string but got %s.',
                is_object($password) ? get_class($password) : gettype($password)
            ));
        }

        $user_info = $user;
        if ($password) {
            $user_info .= ':'.$password;
        }

        $new = clone $this;
        $new->user_info = $user_info;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withHost($host): UriInterface
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string but got %s.',
                is_object($host) ? get_class($host) : gettype($host)
            ));
        }

        $host = strtolower($host);
        if ($this->host == $host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPort($port): UriInterface
    {
        if ($this->port == $port) {
            return $this;
        }

        if ($port && !is_numeric($port)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid port "%s" specified. It must be an integer, an integer string, or null.',
                is_object($port) ? get_class($port) : gettype($port)
            ));
        }

        if ($port && ($port < 1 || $port > 65535)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid port "%d" specified. It must be a valid TCP/UDP port in range 2..65534.',
                $port
            ));
        }

        $port = (int)$port;

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPath($path): UriInterface
    {
        if ($this->path == $path) {
            return $this;
        }

        if (!is_string($path)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string but got %s.',
                is_object($path) ? get_class($path) : gettype($path)
            ));
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withQuery($query): UriInterface
    {
        if ($this->query == $query) {
            return $this;
        }

        if (!is_string($query)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string but got %s.',
                is_object($query) ? get_class($query) : gettype($query)
            ));
        }

        $query = ltrim($query, '?');

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withFragment($fragment): UriInterface
    {
        if ($this->fragment == $fragment) {
            return $this;
        }

        if (!is_string($fragment)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string but got %s.',
                is_object($fragment) ? get_class($fragment) : gettype($fragment)
            ));
        }

        $fragment = ltrim($fragment, '#');

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * @return bool
     */
    protected function isNonStandardPort(): bool
    {
        return !in_array($this->port, [80, 443]) &&
            (($this->scheme == 'http' && !in_array($this->port, [80, null])) ||
            ($this->scheme == 'https' && !in_array($this->port, [443, null])));
    }
}
