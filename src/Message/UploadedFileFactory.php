<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use const UPLOAD_ERR_OK;

/**
 * Class UploadedFileFactory
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createUploadedFile(StreamInterface $stream, int $size = null, int $error = UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null): UploadedFileInterface
    {
        return new UploadedFile(
            $stream,
            $size ?: $stream->getSize(),
            $error,
            $clientFilename,
            $clientMediaType
        );
    }
}
