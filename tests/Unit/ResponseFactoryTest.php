<?php declare(strict_types=1);

use Borsch\Http\Response;

it('should create a Response instance', function () {
    $response = $this->factory->createResponse();
    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->getStatusCode())->toBe(200)
        ->and($response->getReasonPhrase())->toBe('OK');
});

it('should set the response status code', function () {
    $response = $this->factory->createResponse(404);
    expect($response->getStatusCode())->toBe(404);
});

it('should set the response reason phrase', function () {
    $response = $this->factory->createResponse(404, 'Not FOUND');
    expect($response->getReasonPhrase())->toBe('Not FOUND');
});

it('should set the default reason phrase', function () {
    $response = $this->factory->createResponse(404);
    expect($response->getReasonPhrase())->toBe('Not Found');
});