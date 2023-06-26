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
