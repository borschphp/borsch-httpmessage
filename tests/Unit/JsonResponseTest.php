<?php declare(strict_types=1);

use Borsch\Http\Response\JsonResponse;
use Borsch\Http\Stream;
use Psr\Http\Message\StreamInterface;

it('should create a JsonResponse instance', function () {
    $data = ['foo' => 'bar'];
    $response = new JsonResponse($data);
    expect($response)->toBeInstanceOf(JsonResponse::class);
});

it('should set the response status code', function () {
    $data = ['foo' => 'bar'];
    $response = new JsonResponse($data, 201);
    expect($response->getStatusCode())->toBe(201);
});

it('should set the response headers', function () {
    $data = ['foo' => 'bar'];
    $response = new JsonResponse($data);
    expect($response->getHeaders())->toBe([
        'Content-Type' => ['application/json']
    ]);
});

it('should set the response body as json', function () {
    $data = ['foo' => 'bar'];
    $response = new JsonResponse($data);
    expect((string)$response->getBody())->toBe(json_encode($data));
});

it('should set the response body as stream', function () {
    $stream = new Stream('php://temp', 'wb+');
    $stream->write('{"foo":"bar"}');
    $stream->rewind();
    $response = new JsonResponse($stream);
    expect($response->getBody())->toBeInstanceOf(StreamInterface::class)
        ->and($response->getBody()->getContents())->toBe('{"foo":"bar"}');
});

it('should set the response flag', function () {
    $response = new JsonResponse(['foo' => 'bar'], 200, JSON_PRETTY_PRINT);
    expect($response->getBody()->__toString())->toBe("{\n    \"foo\": \"bar\"\n}");
});
