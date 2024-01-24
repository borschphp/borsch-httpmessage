<?php declare(strict_types=1);

use Psr\Http\Message\StreamInterface;

it('should create a stream from a string', function () {
    $stream = $this->factory->createStream('Hello World');
    expect($stream)->toBeInstanceOf(StreamInterface::class)
        ->and((string)$stream)->toEqual('Hello World');
});

it('should create a stream from a file', function () {
    $stream = $this->factory->createStreamFromFile(__FILE__, 'r');
    expect($stream)->toBeInstanceOf(StreamInterface::class)
        ->and((string)$stream)->toEqual(file_get_contents(__FILE__));
});

it('should create a stream from a resource', function () {
    $file = __DIR__.'/../Assets/uploaded_file';
    $resource = fopen($file, 'r');
    $stream = $this->factory->createStreamFromResource($resource);
    expect($stream)->toBeInstanceOf(StreamInterface::class)
        ->and((string)$stream)->toEqual(file_get_contents($file));
});
