<?php  declare(strict_types=1);

use Borsch\Http\Response\TextResponse;
use Psr\Http\Message\StreamInterface;

it('should create a TextResponse instance', function () {
    $response = new TextResponse();
    expect($response)->toBeInstanceOf(TextResponse::class);
});

it('should set the response status code', function () {
    $response = new TextResponse('', 404);
    expect($response->getStatusCode())->toBe(404);
});

it('should set the response body as stream', function () {
    $response = new TextResponse('Hello World!');
    expect($response->getBody())->toBeInstanceOf(StreamInterface::class)
        ->and($response->getBody()->__toString())->toBe('Hello World!');
});

it('should set the content type header', function () {
    $response = new TextResponse();
    expect($response->getHeaderLine('Content-Type'))->toBe('text/plain; charset=utf-8');
});

it('should have status code 200 by default', function () {
    $response = new TextResponse();
    expect($response->getStatusCode())->toBe(200);
});
