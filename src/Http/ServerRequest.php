<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use Borsch\Http\Exception\InvalidArgumentException;
use Psr\Http\Message\{ServerRequestInterface, StreamInterface, UploadedFileInterface, UriInterface};

/**
 * Class ServerRequest
 */
class ServerRequest extends Message implements ServerRequestInterface
{

    protected array $attributes = [];

    protected string $request_target;

    public function __construct(
        protected string $method,
        protected UriInterface $uri,
        array $headers = [],
        StreamInterface $body = null,
        protected array $server_params = [],
        protected array $cookies = [],
        protected array $queryParams = [],
        protected array $uploadedFiles = [],
        protected null|array|object $parsed_body = null,
        string $protocol = '1.1'
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

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getServerParams(): array
    {
        return $this->server_params;
    }

    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function getParsedBody(): object|array|null
    {
        return $this->parsed_body;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withMethod($method): ServerRequestInterface
    {
        $new = clone $this;
        $new->method = $method;

        return $new;
    }

    public function withUri(UriInterface $uri, $preserve_host = false): ServerRequestInterface
    {
        $new = clone $this;
        $new->uri = $uri;

        return $new;
    }

    public function withServerParams(array $server_params): ServerRequestInterface
    {
        $new = clone $this;
        $new->server_params = $server_params;

        return $new;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookies = $cookies;

        return $new;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;

        return $new;
    }

    public function withUploadedFiles(array $uploaded_files): ServerRequestInterface
    {
        $this->validateUploadedFiles($uploaded_files);

        $new = clone $this;
        $new->uploadedFiles = $uploaded_files;

        return $new;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $new = clone $this;
        $new->parsed_body = $data;

        return $new;
    }

    public function withAttribute($name, $value): ServerRequestInterface
    {
        if (!is_string($name) || empty($name)) {
            throw InvalidArgumentException::invalid('attribute name');
        }

        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    public function withoutAttribute($name): ServerRequestInterface
    {
        if (!is_string($name) || empty($name)) {
            throw InvalidArgumentException::invalid('attribute name');
        }

        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }

    protected function validateUploadedFiles(array $uploaded_files)
    {
        foreach ($uploaded_files as $file) {
            if (!$file instanceof UploadedFileInterface) {
                throw InvalidArgumentException::invalidUploadedFile();
            }
        }
    }

    public function getAttribute($name, $default = null)
    {
        if (!is_string($name) || empty($name)) {
            throw InvalidArgumentException::invalid('attribute name');
        }

        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    public function getRequestTarget(): string
    {
        return $this->request_target;
    }

    public function withRequestTarget($request_target): ServerRequestInterface
    {
        if (preg_match('#\s#', $request_target)) {
            throw InvalidArgumentException::invalid('request target provided; cannot contain whitespace');
        }

        $new = clone $this;
        $new->request_target = $request_target;

        return $new;
    }
}
