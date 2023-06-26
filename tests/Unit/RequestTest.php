<?php declare(strict_types=1);

use Borsch\Http\Stream;
use Borsch\Http\Uri;

test('construct without parameter', function () {
    expect($this->request->getMethod())->toBe('GET')
        ->and($this->request->getRequestTarget())->toBe('')
        ->and($this->request->getProtocolVersion())->toBe('1.1');
});

it('should construct with full parameter', function () {
    $uri = new Uri('http://example.com:8080/path?query=value#fragment');
    $body = new Stream('php://temp', 'r+');
    $headers = [
        'Host' => ['example.com'],
        'User-Agent' => ['Borsch']
    ];
    $this->request = $this->request
        ->withUri($uri)
        ->withBody($body)
        ->withHeader('Host', ['example.com'])
        ->withHeader('User-Agent', ['Borsch']);
    expect($this->request->getMethod())->toBe('GET')
        ->and($this->request->getUri())->toBe($uri)
        ->and($this->request->getHeaders())->toBe($headers)
        ->and($this->request->getProtocolVersion())->toBe('1.1')
        ->and($this->request->getBody())->toBe($body);
});

it('should return the request method', function () {
    expect($this->request->getMethod())->toBe('GET');
});

it('should return a new instance with the specified method', function () {
    $new_request = $this->request->withMethod('POST');
    expect($new_request->getMethod())->toBe('POST')
        ->and($this->request->getMethod())->toBe('GET');
});

it('should return the request URI', function () {
    expect($this->request->getUri())->toBeInstanceOf(\Psr\Http\Message\UriInterface::class)
        ->and((string)$this->request->getUri())->toBe('https://example.com');
});

it('should return a new instance with the specified URI', function () {
    $new_request = $this->request->withUri(new Uri('https://example.org'));
    expect((string)$new_request->getUri())->toBe('https://example.org')
        ->and((string)$this->request->getUri())->toBe('https://example.com');
});

test('should return a new instance even if the uri is the same', function () {
    $uri = new Uri('https://example.com');
    $new_request = $this->request->withUri($uri);

    expect($this->request)->not()->toBe($new_request)
        ->and((string)$this->request->getUri())->toBe((string)$new_request->getUri());
});

it('should return the protocol version', function () {
    expect($this->request->getProtocolVersion())->toBe('1.1');
});

test('should return a new instance with the new request target', function () {
    $request = $this->request->withRequestTarget('/test');
    expect($request->getRequestTarget())->toBe('/test');
});

test('should return a new instance even if the request target is the same', function () {
    $request = $this->request->withRequestTarget('/test');
    $new_request = $this->request->withRequestTarget('/test');

    expect($request)->not->toBe($new_request)
        ->and($request->getRequestTarget())->toBe($new_request->getRequestTarget());
});

it('should return the headers', function () {
    $headers = ['Host' => ['example.com'], 'User-Agent' => ['Borsch']];
    expect($this->request->getHeaders())->toBe($headers);
});

it('should return a new instance with the specified headers', function () {
    $headers = ['Host' => ['example.org'], 'User-Agent' => ['Borsch']];
    $new_request = $this->request->withHeader('Host', ['example.org']);
    expect($new_request->getHeaders())->toBe($headers);
});

it('should return a new instance with added headers', function () {
    $headers = ['Host' => ['example.com'], 'User-Agent' => ['Borsch'], 'Accept' => ['application/json']];
    $new_request = $this->request->withAddedHeader('Accept', 'application/json');
    expect($new_request->getHeaders())->toBe($headers);
});

it('should return a new instance without the specified header', function () {
    $headers = ['User-Agent' => ['Borsch']];
    $new_request = $this->request->withoutHeader('Host');
    expect($new_request->getHeaders())->toBe($headers);
});

it('should return the header value', function () {
    expect($this->request->getHeader('User-Agent'))->toBe(['Borsch']);
});

it('should return the header line', function () {
    expect($this->request->getHeaderLine('User-Agent'))->toBe('Borsch');
});
