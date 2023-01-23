<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use InvalidArgumentException;
use Psr\Http\Message\{ServerRequestInterface, StreamInterface, UploadedFileInterface, UriInterface};

/**
 * Class ServerRequest
 */
class ServerRequest extends Message implements ServerRequestInterface
{

    protected array $attributes = [];

    protected null|array|object $parsedBody;

    protected string $request_target;

    public function __construct(
        protected string $method,
        protected UriInterface $uri,
        array $headers = [],
        StreamInterface $body = null,
        protected array $serverParams = [],
        protected array $cookies = [],
        protected array $queryParams = [],
        protected array $uploadedFiles = [],
        $parsedBody = null,
        string $protocol = '1.1'
    ) {
        parent::__construct($protocol, $body, $headers);

        $this->parsedBody = $parsedBody;
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
        return $this->serverParams;
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

    public function getParsedBody()
    {
        return $this->parsedBody;
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

    public function withUri(UriInterface $uri, $preserveHost = false): ServerRequestInterface
    {
        $new = clone $this;
        $new->uri = $uri;

        return $new;
    }

    public function withServerParams(array $serverParams): ServerRequestInterface
    {
        $new = clone $this;
        $new->serverParams = $serverParams;

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

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $this->validateUploadedFiles($uploadedFiles);

        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;

        return $new;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    public function withAttribute($name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    public function withoutAttribute($name): ServerRequestInterface
    {
        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }

    protected function validateUploadedFiles(array $uploadedFiles)
    {
        foreach ($uploadedFiles as $file) {
            if (!$file instanceof UploadedFileInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid value in uploaded files specification; must be an instance of %s',
                    UploadedFileInterface::class
                ));
            }
        }
    }

    public function getAttribute($name, $default = null)
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

    public function withRequestTarget($requestTarget): ServerRequestInterface
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; cannot contain whitespace'
            );
        }

        $new = clone $this;
        $new->request_target = $requestTarget;

        return $new;
    }
}
