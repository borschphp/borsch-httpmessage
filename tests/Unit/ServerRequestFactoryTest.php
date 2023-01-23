<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

use Borsch\Http\ServerRequest;
use Borsch\Http\Stream;
use Borsch\Http\UploadedFile;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

it('should create a ServerRequest instance', function () {
    $request = $this->factory->createServerRequest('GET', '/');
    expect($request)->toBeInstanceOf(ServerRequest::class);
});

it('should set the request method', function () {
    $request = $this->factory->createServerRequest('GET', '/');
    expect($request->getMethod())->toBe('GET');
});

it('should set the request URI', function () {
    $request = $this->factory->createServerRequest('GET', 'http://example.com/');
    expect($request->getUri()->__toString())->toBe('http://example.com/');
});

it('should set the request headers', function () {
    $serverParams = [
        'HTTP_ACCEPT' => ['application/json'],
        'HTTP_CONTENT_TYPE' => ['application/json'],
    ];
    $request = $this->factory->createServerRequest('GET', '/', $serverParams);
    expect($request->getHeaders())->toBe([
        'ACCEPT' => ['application/json'],
        'CONTENT-TYPE' => ['application/json'],
    ]);
});

it('should set the request cookies', function () {
    $serverParams = [
        'cookie' => [
            'foo' => 'bar',
            'baz' => 'qux',
        ],
    ];
    $request = $this->factory->createServerRequest('GET', '/', $serverParams);
    expect($request->getCookieParams())->toBe([
        'foo' => 'bar',
        'baz' => 'qux',
    ]);
});

it('should set the request query params', function () {
    $serverParams = [
        'QUERY_STRING' => 'foo=bar&baz=qux',
    ];
    $request = $this->factory->createServerRequest('GET', 'http://example.com/?foo=bar&baz=qux', $serverParams);
    expect($request->getQueryParams())->toBe([
        'foo' => 'bar',
        'baz' => 'qux',
    ]);
});

it('should set the request uploaded file', function () {
    $uploaded_file = new UploadedFile(
        new Stream('php://temp', 'wb+'),
        0,
        UPLOAD_ERR_OK,
        'file',
        'text/plain'
    );
    $request = $this->factory->createServerRequest('POST', '/', [
        'FILES_file' => [
            'name' => 'file',
            'type' => 'text/plain',
            'tmp_name' => 'php://temp',
            'error' => UPLOAD_ERR_OK,
            'size' => 0
        ]
    ]);
    expect($request->getUploadedFiles())->toBeArray()->toHaveKey('file')
        ->and($request->getUploadedFiles()['file']->getStream())->toBeInstanceOf(StreamInterface::class)
        ->and($request->getUploadedFiles()['file']->getSize())->toBe(0)
        ->and($request->getUploadedFiles()['file']->getError())->toBe(UPLOAD_ERR_OK)
        ->and($request->getUploadedFiles()['file']->getClientFilename())->toBe('file')
        ->and($request->getUploadedFiles()['file']->getClientMediaType())->toBe('text/plain');
});

it('should set the request parsed body', function () {
    $request = $this->factory->createServerRequest('POST', '/', [
        'request_body' => [
            'foo' => 'bar',
            'baz' => 'bat'
        ]
    ]);
    expect($request->getParsedBody())->toEqual([
        'foo' => 'bar',
        'baz' => 'bat'
    ]);
});

it('should set the request query parameters', function () {
    $request = $this->factory->createServerRequest('GET', '/?foo=bar&baz=bat');
    expect($request->getQueryParams())->toEqual([
        'foo' => 'bar',
        'baz' => 'bat'
    ]);
});

it('should set the request body', function () {
    $body = 'Hello World';
    $request = $this->factory->createServerRequest('GET', 'https://example.com', ['php://input' => $body]);
    expect((string)$request->getBody())->toEqual($body);
});

it('should set the request uploaded files', function () {
    $uploaded_files = [
        'FILES_file_1' => [
            'tmp_name' => __DIR__.'/../Assets/uploaded_file',
            'size' => 100,
            'error' => UPLOAD_ERR_OK,
            'name' => 'file_1.txt',
            'type' => 'text/plain',
        ],
        'FILES_file_2' => [
            'tmp_name' => __DIR__.'/../Assets/uploaded_file',
            'size' => 200,
            'error' => UPLOAD_ERR_OK,
            'name' => 'file_2.txt',
            'type' => 'text/plain',
        ],
    ];
    $request = $this->factory->createServerRequest('POST', 'https://example.com', $uploaded_files);
    expect($request->getUploadedFiles())->toBeArray()->toHaveLength(2)
        ->and($request->getUploadedFiles())->toBeArray()->toHaveKey('file_1')
        ->and($request->getUploadedFiles()['file_1'])->toBeInstanceOf(UploadedFileInterface::class)
        ->and($request->getUploadedFiles()['file_1']->getSize())->toBe(100)
        ->and($request->getUploadedFiles()['file_1']->getError())->toBe(UPLOAD_ERR_OK)
        ->and($request->getUploadedFiles()['file_1']->getClientFilename())->toBe('file_1.txt')
        ->and($request->getUploadedFiles()['file_1']->getClientMediaType())->toBe('text/plain')
        ->and($request->getUploadedFiles())->toBeArray()->toHaveKey('file_2')
        ->and($request->getUploadedFiles()['file_2'])->toBeInstanceOf(UploadedFileInterface::class)
        ->and($request->getUploadedFiles()['file_2']->getSize())->toBe(200)
        ->and($request->getUploadedFiles()['file_2']->getError())->toBe(UPLOAD_ERR_OK)
        ->and($request->getUploadedFiles()['file_2']->getClientFilename())->toBe('file_2.txt')
        ->and($request->getUploadedFiles()['file_2']->getClientMediaType())->toBe('text/plain');
});
