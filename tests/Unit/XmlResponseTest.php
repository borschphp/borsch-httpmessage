<?php declare(strict_types=1);

use Borsch\Http\Response\XmlResponse;
use Borsch\Http\Stream;
use Psr\Http\Message\StreamInterface;

it('should set the response body as stream', function () {
    $xml = new SimpleXMLElement('<foo><bar>baz</bar></foo>');
    $response = new XmlResponse($xml);
    expect($response->getBody())->toBeInstanceOf(StreamInterface::class);
});

it('should set the response body as stream with string', function () {
    $response = new XmlResponse('<foo><bar>baz</bar></foo>');
    expect($response->getBody())->toBeInstanceOf(StreamInterface::class);
});

it('should set the response body as stream with stream', function () {
    $stream = new Stream('php://temp', 'wb+');
    $stream->write('<foo><bar>baz</bar></foo>');
    $response = new XmlResponse($stream);
    expect($response->getBody())->toBeInstanceOf(StreamInterface::class);
});

it('should set the response content-type', function () {
    $response = new XmlResponse();
    expect($response->getHeaderLine('content-type'))->toBe('application/xml; charset=utf-8');
});

it('should set the response status code', function () {
    $response = new XmlResponse('', 404);
    expect($response->getStatusCode())->toBe(404);
});
