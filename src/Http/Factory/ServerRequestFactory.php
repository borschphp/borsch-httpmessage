<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Factory;

use Exception;
use Borsch\Http\{
    Exception\InvalidArgumentException,
    Exception\RuntimeException,
    ServerRequest,
    Stream,
    UploadedFile,
    Uri
};
use Psr\Http\Message\{
    ServerRequestFactoryInterface,
    ServerRequestInterface,
    StreamInterface,
    UploadedFileInterface,
    UriInterface
};

/**
 * Class ServerRequestFactory
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{

    public function createServerRequest(string $method, $uri, array $server_params = []): ServerRequestInterface
    {
        if (!is_string($uri) && !$uri instanceof UriInterface) {
            throw InvalidArgumentException::mustBeAStringOrAnInstanceOf('Uri', UriInterface::class);
        }

        if (!$uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }

        $headers = $this->getHeadersFromServerParams($server_params);
        $body = $this->getStreamFromServerParams($server_params);
        $cookies = $this->getCookiesFromServerParams($server_params);
        $queryParams = $this->getQueryParamsFromUri($uri);
        $uploadedFiles = $this->getUploadedFilesFromServerParams($server_params);
        $parsedBody = $this->getParsedBodyFromServerParams($server_params);

        return new ServerRequest(
            $method,
            $uri,
            $headers,
            $body,
            $server_params,
            $cookies,
            $queryParams,
            $uploadedFiles,
            $parsedBody
        );
    }

    public function createServerRequestFromGlobals(): ServerRequestInterface
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $headers = getallheaders() ?? [];
        $uri = new Uri(
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http').'://'.
            $_SERVER['HTTP_HOST'].
            $_SERVER['REQUEST_URI']
        );
        $body = new Stream('php://input', 'r');
        $server_params = $_SERVER;
        $cookies = $_COOKIE;
        $query_params = $_GET;
        $parsed_body = $_POST;
        $uploaded_files = $this->getUploadedFilesFromServerParams(array_combine(
            array_map(fn($key) => 'FILES_'.$key, array_keys($_FILES)),
            $_FILES
        ));

        return new ServerRequest(
            $method,
            $uri,
            $headers,
            $body,
            $server_params,
            $cookies,
            $query_params,
            $parsed_body,
            $uploaded_files
        );
    }

    private function getHeadersFromServerParams(array $server_params): array
    {
        $headers = [];
        foreach ($server_params as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    private function getStreamFromServerParams(array $server_params): StreamInterface
    {
        $stream = new Stream('php://temp', 'wb+');
        $stream->write($server_params['php://input'] ?? '');

        return $stream;
    }

    private function getCookiesFromServerParams(array $server_params): array
    {
        return $server_params['cookie'] ?? [];
    }

    private function getQueryParamsFromUri(UriInterface $uri): array
    {
        parse_str($uri->getQuery(), $query);

        return $query;
    }

    private function getUploadedFilesFromServerParams(array $server_params): array
    {
        $files = [];
        foreach ($server_params as $key => $value) {
            if (str_starts_with($key, 'FILES_')) {
                $name = substr($key, 6);
                $files[$name] = $this->normalizeUploadedFile($value);
            }
        }

        return $files;
    }

    private function getParsedBodyFromServerParams(array $server_params): array
    {
        if (isset($server_params['request_body'])) {
            return $server_params['request_body'];
        }

        return [];
    }

    private function normalizeUploadedFile(array $file): UploadedFileInterface
    {
        $required_keys = ['tmp_name', 'size', 'error', 'name', 'type'];
        $missing_keys = array_diff($required_keys, array_keys($file));

        if (!empty($missing_keys)) {
            throw new InvalidArgumentException('Missing keys in file data: '.implode(', ', $missing_keys));
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw RuntimeException::fileError($file['error']);
        }

        try {
            $stream = new Stream($file['tmp_name'], 'rb');
        } catch (Exception $e) {
            throw RuntimeException::unableToCreateStream($e->getMessage());
        }

        return new UploadedFile(
            $stream,
            $file['size'],
            $file['error'],
            $file['name'],
            $file['type']
        );
    }
}
