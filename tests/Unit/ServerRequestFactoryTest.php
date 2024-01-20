<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

use Borsch\Http\ServerRequest;
use Borsch\Http\Stream;
use Borsch\Http\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
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

test('createServerRequestFromGlobals with default values', function () {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['HTTP_HOST'] = 'example.com';
    $_SERVER['REQUEST_URI'] = '/index.html';
    $_GET['foo'] = 'bar';
    $_FILES = [
        'file1' => [
            'name' => 'file1.txt',
            'type' => 'text/plain',
            'tmp_name' => __DIR__.'/../Assets/uploaded_file',
            'error' => UPLOAD_ERR_OK,
            'size' => 11
        ]
    ];

    /** @var ServerRequestInterface $request */
    $request = $this->factory->createServerRequestFromGlobals();

    expect($request->getMethod())->toBe('GET')
        ->and($request->getHeaders())->toBeArray()
        ->and($request->getHeaders())->not->toBeEmpty()
        ->and($request->getUri()->__toString())->toBe('https://example.com/index.html')
        ->and($request->getCookieParams())->toBe($_COOKIE)
        ->and($request->getQueryParams())->toBe($_GET)
        ->and($request->getUploadedFiles())->toBeArray()
        ->and($request->getUploadedFiles())->toHaveCount(1);

    /** @var UploadedFileInterface $uploaded_file */
    $uploaded_file = $request->getUploadedFiles()['file1'];
    expect($uploaded_file)->toBeInstanceOf(UploadedFileInterface::class)
        ->and($uploaded_file->getError())->toBe(UPLOAD_ERR_OK)
        ->and($uploaded_file->getSize())->toBe(11)
        ->and($uploaded_file->getClientFilename())->toBe('file1.txt')
        ->and($uploaded_file->getClientMediaType())->toBe('text/plain');
});
