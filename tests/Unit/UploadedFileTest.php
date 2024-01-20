<?php declare(strict_types=1);

use Psr\Http\Message\StreamInterface;

it('should construct with full parameter', function () {
    expect($this->uploaded_file->getStream())->toBeInstanceOf(StreamInterface::class)
        ->and($this->uploaded_file->getSize())->toBe(11)
        ->and($this->uploaded_file->getError())->toBe(UPLOAD_ERR_OK)
        ->and($this->uploaded_file->getClientFilename())->toBe('uploaded_file')
        ->and($this->uploaded_file->getClientMediaType())->toBe('plain/text');
});

it('should move uploaded file', function () {
    $path = __DIR__ . '/uploaded_file.txt';
    $this->uploaded_file->moveTo($path);
    expect(file_exists($path))->toBeTrue();
});

it('should move uploaded file with different name', function () {
    $path = __DIR__ . '/new_uploaded_file.txt';
    $this->uploaded_file->moveTo($path);
    expect(file_exists($path))->toBeTrue()
        ->and(file_get_contents($path))->toHaveLength(11);
});

it('should throw exception when trying to move file after it has already been moved', function () {
    $path = __DIR__ . '/uploaded_file.txt';
    $this->uploaded_file->moveTo($path);
    $this->uploaded_file->moveTo($path);
})->throws(RuntimeException::class);

it('should throw exception when trying to move file to a non-writable directory', function () {
    $path = '/root/uploaded_file.txt';
    $this->uploaded_file->moveTo($path);
})->throws(RuntimeException::class);

it('should return the file size when calling getSize', function () {
    expect($this->uploaded_file->getSize())->toBe(11);
});

it('should return the error status when calling getError', function () {
    expect($this->uploaded_file->getError())->toBe(UPLOAD_ERR_OK);
});

it('should return the client filename when calling getClientFilename', function () {
    expect($this->uploaded_file->getClientFilename())->toBe('uploaded_file');
});

it('should return the client media type when calling getClientMediaType', function () {
    expect($this->uploaded_file->getClientMediaType())->toBe('plain/text');
});

it('should return the file stream when calling getStream', function () {
    expect($this->uploaded_file->getStream())->toBeInstanceOf(StreamInterface::class);
});

it('should throw exception when trying to get the stream after the file has been moved', function () {
    $path = __DIR__.'/uploaded_file.txt';
    $this->uploaded_file->moveTo($path);
    $this->uploaded_file->getStream();
})->throws(RuntimeException::class);
