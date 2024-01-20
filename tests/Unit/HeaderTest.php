<?php declare(strict_types=1);

use Borsch\Http\Exception\InvalidArgumentException;
use Borsch\Http\Header;

test('constructor should throw exception on empty name', function () {
    new Header('', '');
})->throws(InvalidArgumentException::class);

test('constructor should throw exception on empty values not a string', function () {
    new Header('Content-Type', ['application/json', new stdClass()]);
})->throws(InvalidArgumentException::class);

test('constructor should turn string value to array of string value', function () {
    $header = new Header('Content-Type', 'application/json');
    expect($header->values)->toBeArray()
        ->and($header->values)->toHaveCount(1)
        ->and($header->values)->toBe(['application/json']);
});

test('equals should return true on similar headers', function () {
    $header1 = new Header('Content-Type', 'application/json');
    $header2 = new Header('Content-Type', 'application/json');
    expect($header1->equals($header2))->toBeTrue()
        ->and($header2->equals($header1))->toBeTrue();
});

test('equals should return false on different header names', function () {
    $header1 = new Header('Content-Type', 'application/json');
    $header2 = new Header('Accept', 'application/json');
    expect($header1->equals($header2))->toBeFalse()
        ->and($header2->equals($header1))->toBeFalse();
});

test('equals should return false on different header values', function () {
    $header1 = new Header('Content-Type', 'application/json');
    $header2 = new Header('Content-Type', 'application/xml');
    expect($header1->equals($header2))->toBeFalse()
        ->and($header2->equals($header1))->toBeFalse();
});
