<?php declare(strict_types=1);

use Borsch\Http\Response\EmptyResponse;

it('should set the response status code', function () {
    $response = new EmptyResponse(205);
    expect($response->getStatusCode())->toBe(205);
});

it('should not have a body', function () {
    $response = new EmptyResponse();
    expect($response->getBody()->__toString())->toBe('');
});

it('should have status code 204 by default', function () {
    $response = new EmptyResponse();
    expect($response->getStatusCode())->toBe(204);
});
