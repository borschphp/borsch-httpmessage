<?php declare(strict_types=1);

use Borsch\Http\Uri;

test('construct without parameter', function () {
    expect($this->server_request->getMethod())->toBe('GET')
        ->and($this->server_request->getRequestTarget())->toBe('')
        ->and($this->server_request->getProtocolVersion())->toBe('1.1');
});

test('should construct with full parameter', function () {
    $uri = new Uri('http://example.com:8080/path?query=value#fragment');
    $headers = [
        'Host' => ['example.com'],
        'User-Agent' => ['Borsch']
    ];
    $request = $this->server_request
        ->withUri($uri)
        ->withHeader('Host', ['example.com'])
        ->withHeader('User-Agent', ['Borsch']);

    expect($request->getMethod())->toBe('GET')
        ->and($request->getUri())->toBe($uri)
        ->and($request->getHeaders())->toBe($headers)
        ->and($request->getProtocolVersion())->toBe('1.1');
});

test('should return the request method', function () {
    expect($this->server_request->getMethod())->toBe('GET');
});

test('should return a new instance even if the method is the same', function () {
    $new_request = $this->server_request->withMethod('GET');

    expect($this->server_request)->not->toBe($new_request)
        ->and($this->server_request->getMethod())->toBe($new_request->getMethod());
});

test('should return a new instance with the specified method', function () {
    $new_request = $this->server_request->withMethod('POST');
    expect($new_request->getMethod())->toBe('POST')
        ->and($this->server_request->getMethod())->toBe('GET');
});

test('should return the request URI', function () {
    expect($this->server_request->getUri())->toBeInstanceOf(\Psr\Http\Message\UriInterface::class)
        ->and((string)$this->server_request->getUri())->toBe('https://example.com');
});

test('should return a new instance with the specified URI', function () {
    $new_request = $this->server_request->withUri(new Uri('https://example.org'));
    expect((string)$new_request->getUri())->toBe('https://example.org')
        ->and((string)$this->server_request->getUri())->toBe('https://example.com');
});

test('should return a new instance even if the uri is the same', function () {
    $uri = new Uri('https://example.com');
    $new_request = $this->server_request->withUri($uri);
    expect($this->server_request)->not()->toBe($new_request)
        ->and((string)$this->server_request->getUri())->toBe((string)$new_request->getUri());
});

test('should return the protocol version', function () {
    expect($this->server_request->getProtocolVersion())->toBe('1.1');
});

test('should return a new instance with the new request target', function () {
    $request = $this->server_request->withRequestTarget('/test');
    expect($request->getRequestTarget())->toBe('/test');
});

test('should return a new instance even if the request target is the same', function () {
    $request = $this->server_request->withRequestTarget('/test');
    $new_request = $this->server_request->withRequestTarget('/test');
    expect($request)->not->toBe($new_request)
        ->and($request->getRequestTarget())->toBe($new_request->getRequestTarget());
});

test('should return the server params', function () {
    expect($this->server_request->getServerParams())->toBe([]);
});

test('should return the cookie params', function () {
    expect($this->server_request->getCookieParams())->toBe([]);
});

test('should return the query params', function () {
    expect($this->server_request->getQueryParams())->toBe([]);
});

test('should return the uploaded files', function () {
    expect($this->server_request->getUploadedFiles())->toBe([]);
});

test('should return the cookies', function () {
    expect($this->server_request->getCookieParams())->toBe([]);
});

test('should return a new instance with the specified cookies', function () {
    $new_request = $this->server_request->withCookieParams(['test' => 'value']);
    expect($new_request->getCookieParams())->toBe(['test' => 'value'])
        ->and($this->server_request->getCookieParams())->toBe([]);
});

test('should return a new instance with the specified query params', function () {
    $new_request = $this->server_request->withQueryParams(['test' => 'value']);
    expect($new_request->getQueryParams())->toBe(['test' => 'value'])
        ->and($this->server_request->getQueryParams())->toBe([]);
});

test('should return the upload files', function () {
    expect($this->server_request->getUploadedFiles())->toBe([]);
});

test('should return a new instance with the new protocol version', function () {
    $request = $this->server_request->withProtocolVersion('2.0');
    expect($request->getProtocolVersion())->toBe('2.0');
});

test('should return a new instance even if the protocol version is the same', function () {
    $request = $this->server_request->withProtocolVersion('1.1');
    $new_request = $this->server_request->withProtocolVersion('1.1');
    expect($request)->not->toBe($new_request)
        ->and($request->getProtocolVersion())->toBe($new_request->getProtocolVersion());
});

test('should return a new instance with the new cookie params', function () {
    $params = [
        'foo' => 'bar'
    ];
    $request = $this->server_request->withCookieParams($params);
    expect($request->getCookieParams())->toBe($params);
});

test('should return a new instance even if the cookie params is the same', function () {
    $params = [
        'foo' => 'bar'
    ];
    $request = $this->server_request->withCookieParams($params);
    $new_request = $this->server_request->withCookieParams($params);
    expect($request)->not->toBe($new_request)
        ->and($request->getCookieParams())->toBe($new_request->getCookieParams());
});

test('should return a new instance with the new query params', function () {
    $params = [
        'foo' => 'bar'
    ];
    $request = $this->server_request->withQueryParams($params);
    expect($request->getQueryParams())->toBe($params);
});

test('should return a new instance even if the query params is the same', function () {
    $params = [
        'foo' => 'bar'
    ];
    $request = $this->server_request->withQueryParams($params);
    $new_request = $this->server_request->withQueryParams($params);
    expect($request)->not->toBe($new_request)
        ->and($request->getQueryParams())->toBe($new_request->getQueryParams());
});
