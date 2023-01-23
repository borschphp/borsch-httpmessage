<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Factory;

use Borsch\Http\UploadedFile;
use Psr\Http\Message\{StreamInterface, UploadedFileFactoryInterface, UploadedFileInterface};
use const UPLOAD_ERR_OK;

/**
 * Class UploadedFileFactory
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $client_filename = null,
        string $client_media_type = null
    ): UploadedFileInterface {
        return new UploadedFile($stream, $size, $error, $client_filename, $client_media_type);
    }
}
