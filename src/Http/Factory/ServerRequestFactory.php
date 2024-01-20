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
    UploadedFileInterface,
};
use function getallheaders, array_keys, str_starts_with, substr, array_diff, implode;

/**
 * Class ServerRequestFactory
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{

    public function createServerRequest(string $method, $uri, array $server_params = []): ServerRequestInterface
    {
        return new ServerRequest(
            $method,
            $uri,
            [],
            null,
            $server_params
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

            $uploaded_files,
            $parsed_body
        );
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
