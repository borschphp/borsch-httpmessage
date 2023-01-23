<?php declare(strict_types=1);

use Borsch\Http\Uri;
use Psr\Http\Message\UriInterface;

it('should create a Uri instance', function () {
    $uri = $this->factory->createUri('https://example.com');
    expect($uri)->toBeInstanceOf(UriInterface::class)
        ->and($uri)->toBeInstanceOf(Uri::class);
});

it('should create a Uri instance from a string', function () {
    $uri = $this->factory->createUri('https://example.com');
    expect($uri->__toString())->toEqual('https://example.com');
});
