<?php declare(strict_types=1);

use Borsch\Http\Response\EmptyResponse;

it('should set the response status code', function () {
    $response = new EmptyResponse(204);
    expect($response->getStatusCode())->toBe(204);
});

it('should not have a body', function () {
    $response = new EmptyResponse();
    expect($response->getBody()->__toString())->toBe('');
});
