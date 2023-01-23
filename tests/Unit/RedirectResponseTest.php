<?php declare(strict_types=1);

use Borsch\Http\Response\RedirectResponse;
use Borsch\Http\Uri;
use Psr\Http\Message\ResponseInterface;

it('should create a RedirectResponse instance', function () {
    $response = new RedirectResponse('/');
    expect($response)->toBeInstanceOf(ResponseInterface ::class);
});

it('should set the response status code', function () {
    $response = new RedirectResponse('/', 301);
    expect($response->getStatusCode())->toBe(301);
});

it('should set the location header', function () {
    $response = new RedirectResponse('/');
    expect($response->getHeader('location'))->toBeArray()->toContain('/');
});

it('should set the location header with UriInterface', function () {
    $response = new RedirectResponse(new Uri('/'));
    expect($response->getHeader('location'))->toBeArray()->toContain('/');
});

it('should throw InvalidArgumentException if invalid uri provided', function () {
    new RedirectResponse([]);
})->throws(TypeError::class);
