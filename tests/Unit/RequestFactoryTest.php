<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

use Borsch\Http\Request;

it('should create a Request instance', function () {
    $request = $this->factory->createRequest('GET', '/');
    expect($request)->toBeInstanceOf(Request::class);
});

it('should set the request method', function () {
    $request = $this->factory->createRequest('PUT', '/');
    expect($request->getMethod())->toBe('PUT');
});

it('should set the request URI', function () {
    $request = $this->factory->createRequest('GET', 'http://example.com/');
    expect($request->getUri()->__toString())->toBe('http://example.com/');
});

it('should throw a TypeError when creating a request with invalid URI', function () {
    $this->factory->createRequest('GET', []);
})->throws(TypeError::class);
