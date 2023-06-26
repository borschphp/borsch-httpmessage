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
class ServerRequest extends Request implements ServerRequestInterface
{

    protected array $attributes = [];

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
        parent::__construct($method, $uri, $protocol, $body, $headers);
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

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        if (empty($name)) {
            throw InvalidArgumentException::invalid('attribute name');
        }

        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }

    protected function validateUploadedFiles(array $uploaded_files): void
    {
        foreach ($uploaded_files as $file) {
            if (!$file instanceof UploadedFileInterface) {
                throw InvalidArgumentException::invalidUploadedFile();
            }
        }
    }

    public function getAttribute(string $name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    public function getRequestTarget(): string
    {
        return $this->request_target;
    }
}
