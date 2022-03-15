<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequest
 */
class ServerRequest extends Request implements ServerRequestInterface
{

    /** @var array */
    protected $server_params = [];

    /** @var array */
    protected $cookie_params = [];

    /** @var array */
    protected $query_params = [];

    /** @var array */
    protected $uploaded_files = [];

    /** @var object|array|null */
    protected $parsed_body;

    /** @var array */
    protected $attributes = [];

    /**
     * @param array $serverParams
     */
    public function __construct(array $serverParams = [])
    {
        $this->server_params = $serverParams;
    }

    /**
     * @inheritDoc
     */
    public function getServerParams(): array
    {
        return $this->server_params;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array
    {
        return $this->cookie_params;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookie_params = $cookies;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->query_params;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->query_params = $query;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles(): array
    {
        return $this->uploaded_files;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        array_walk_recursive($uploadedFiles, function ($file) {
            if (!$file instanceof UploadedFileInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid uploaded file, expected UploadedFileInterface but got %s.',
                    is_object($file) ? get_class($file) : gettype($file)
                ));
            }
        });

        $new = clone $this;
        $new->uploaded_files = $uploadedFiles;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody()
    {
        return $this->parsed_body;
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody($data)
    {
        if (!is_array($data) && !is_object($data) && $data !== null) {
            throw new InvalidArgumentException(sprintf(
                'Invalid Parsed Body. Expected null, array or object but got %s.',
                gettype($data)
            ));
        }

        $new = clone $this;
        $new->parsed_body = $data;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute($name, $value)
    {
        if (isset($this->attributes[$name]) && $this->attributes[$name] === $value) {
            return $this;
        }

        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            return $this;
        }

        $new = clone $this;

        unset($new->attributes[$name]);

        return $new;
    }
}
