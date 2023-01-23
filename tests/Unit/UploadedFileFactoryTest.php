<?php declare(strict_types=1);

use Borsch\Http\Stream;
use Borsch\Http\UploadedFile;
use Psr\Http\Message\StreamInterface;

it('should create an UploadedFile instance', function () {
    $uploadedFile = $this->factory->createUploadedFile(
        new Stream(__DIR__.'/../Assets/uploaded_file'),
        11,
        UPLOAD_ERR_OK,
        'file',
        'text/plain'
    );
    expect($uploadedFile)->toBeInstanceOf(UploadedFile::class);
});

it('should set the uploaded file properties', function () {
    $stream = new Stream(__DIR__.'/../Assets/uploaded_file');
    $uploadedFile = $this->factory->createUploadedFile(
        $stream,
        11,
        UPLOAD_ERR_OK,
        'file',
        'text/plain'
    );
    expect($uploadedFile->getStream())->toBe($stream)
        ->and($uploadedFile->getSize())->toBe(11)
        ->and($uploadedFile->getError())->toBe(UPLOAD_ERR_OK)
        ->and($uploadedFile->getClientFilename())->toBe('file')
        ->and($uploadedFile->getClientMediaType())->toBe('text/plain');
});
