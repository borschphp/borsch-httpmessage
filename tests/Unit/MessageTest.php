<?php declare(strict_types=1);

use Borsch\Http\Header;
use Borsch\Http\Stream;
use Psr\Http\Message\StreamInterface;

test('getProtocolVersion method', function () {
    expect($this->message->getProtocolVersion())->toBe('1.1');
});

test('withProtocolVersion method', function () {
    $new_message = $this->message->withProtocolVersion('2.0');
    expect($new_message->getProtocolVersion())->toBe('2.0');
});

test('getHeaders method', function () {
    expect($this->message->getHeaders())->toBeArray()->toBeEmpty();
});

test('hasHeader method', function () {
    expect($this->message->hasHeader('Content-Type'))->toBeFalse();
});

test('getHeader method', function () {
    expect($this->message->getHeader('Content-Type'))->toBeArray()->toBeEmpty();
});

test('getHeaderLine method', function () {
    expect($this->message->getHeaderLine('Content-Type'))->toBe('');
});

test('withHeader method', function () {
    $new_message = $this->message->withHeader('Content-Type', 'application/json');
    expect($new_message->hasHeader('Content-Type'))->toBeTrue()
        ->and($new_message->getHeader('Content-Type'))->toBe(['application/json'])
        ->and($new_message->getHeaderLine('Content-Type'))->toBe('application/json');
});

test('withAddedHeader method', function () {
    $new_message = $this->message->withAddedHeader('Content-Type', 'application/json');
    expect($new_message->hasHeader('Content-Type'))->toBeTrue()
        ->and($new_message->getHeader('Content-Type'))->toBe(['application/json'])
        ->and($new_message->getHeaderLine('Content-Type'))->toBe('application/json');
});

test('withAddedHeader method with different case headers', function () {
    $new_message = $this->message->withAddedHeader('Content-Type', 'application/json');
    $new_message = $new_message->withAddedHeader('Content-Type', 'application/xml');
    expect($new_message->hasHeader('Content-Type'))->toBeTrue()
        ->and($new_message->getHeader('Content-Type'))->toBe(['application/json', 'application/xml'])
        ->and($new_message->getHeaderLine('Content-Type'))->toBe('application/json,application/xml');
});

test('withoutHeader method', function () {
    $new_message = $this->message->withHeader('Content-Type', 'application/json')->withoutHeader('Content-Type');
    expect($new_message->hasHeader('Content-Type'))->toBeFalse()
        ->and($new_message->getHeader('Content-Type'))->toBeArray()->toBeEmpty()
        ->and($new_message->getHeaderLine('Content-Type'))->toBe('');
});

test('getBody method', function () {
    expect($this->message->getBody())->toBeInstanceOf(StreamInterface::class);
});

test('withBody method', function () {
    $stream = new Stream();
    $stream->write('{"data":"test"}');
    $stream->rewind();
    $new_message = $this->message->withBody($stream);
    expect($new_message->getBody()->getContents())->toBe('{"data":"test"}');
});

test('withProtocolVersion method with invalid input', function () {
    $this->message->withProtocolVersion(2);
})->throws(TypeError::class);

test('withHeader method with invalid input', function () {
    $this->message->withHeader('', 'application/json');
})->throws(InvalidArgumentException::class);

test('withAddedHeader method with invalid input', function () {
    $this->message->withAddedHeader('', 'application/json');
})->throws(InvalidArgumentException::class);

test('withHeader method with extra spaces', function () {
    $new_message = $this->message->withHeader('Content-Type', ' application/json ');
    expect($new_message->getHeaderLine('Content-Type'))->not()->toBe('application/json')
        ->and($new_message->getHeaderLine('Content-Type'))->toBe(' application/json ');
});

test('withoutHeader method with non-string input', function () {
    $this->message->withoutHeader(1);
})->throws(TypeError::class);
