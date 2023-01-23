<?php declare(strict_types=1);

use Borsch\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

test('construct without parameter', function () {
    $response = new Response();

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getReasonPhrase())->toBe('OK')
        ->and($response->getBody())->toBeInstanceOf(StreamInterface::class)
        ->and($response->getHeaders())->toBeArray()->toBeEmpty();
});

test('should construct with full parameter', function () {
    expect($this->response->getStatusCode())->toBe(200)
        ->and($this->response->getReasonPhrase())->toBe('OK')
        ->and($this->response->getBody())->toBeInstanceOf(StreamInterface::class)
        ->and($this->response->getBody()->getContents())->toBe('Borsch')
        ->and($this->response->getHeaders())->toBe(['Host' => ['example.com'], 'User-Agent' => ['Borsch']]);
});

test('withStatus should throw InvalidArgumentException with invalid status code 600', function () {
    $this->response->withStatus(600);
})->throws(InvalidArgumentException::class);

test('withStatus should throw InvalidArgumentException with invalid status code 99', function () {
    $this->response->withStatus(99);
})->throws(InvalidArgumentException::class);

test('withStatus should throw InvalidArgumentException with invalid reason phrase', function () {
    $this->response->withStatus(200, []);
})->throws(InvalidArgumentException::class);

test('withStatus should return new instance with updated status code and reason phrase', function () {
    $new = $this->response->withStatus(404, 'Not Found');
    expect($new)->toBeInstanceOf(ResponseInterface::class)
        ->and($new)->not->toBe($this->response)
        ->and($new->getStatusCode())->toBe(404)
        ->and($new->getReasonPhrase())->toBe('Not Found');
});

test('withStatus should use default reason phrase if none provided', function () {
    $new = $this->response->withStatus(404);
    expect($new->getReasonPhrase())->toBe('Not Found');
});
