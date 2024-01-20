<?php declare(strict_types=1);

use Borsch\Http\Response\HtmlResponse;
use Psr\Http\Message\{ResponseInterface, StreamInterface};

it('should create a HtmlResponse instance', function () {
    $response = new HtmlResponse();
    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

it('should set the response status code', function () {
    $response = new HtmlResponse('', 404);
    expect($response->getStatusCode())->toBe(404);
});

it('should set the response body as stream', function () {
    $html = '<html><body>Hello World!</body></html>';
    $response = new HtmlResponse($html);
    expect($response->getBody())->toBeInstanceOf(StreamInterface::class)
        ->and($response->getBody()->__toString())->toBe($html);
});

it('should set the response headers', function () {
    $response = new HtmlResponse();
    expect($response->getHeaders())->toBe([
        'Content-Type' => ['text/html; charset=utf-8']
    ]);
});

it('should have status code 200 by default', function () {
    $response = new HtmlResponse();
    expect($response->getStatusCode())->toBe(200);
});
