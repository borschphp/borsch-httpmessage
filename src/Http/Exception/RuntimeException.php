<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Exception;

/**
 * Class RuntimeException
 */
class RuntimeException extends \RuntimeException
{

    public static function noResourceAvailableCantDoAction(string $action): static
    {
        return new static(sprintf('No resource available; cannot %s.', $action));
    }

    public static function errorOccurredDuringMethodCall(string $method_name): static
    {
        return new static(sprintf(
            'Error occurred during %s operation.',
            $method_name
        ));
    }

    public static function streamIsNotReadable(): static
    {
        return new static('Stream is not readable.');
    }

    public static function streamIsNotSeekable(): static
    {
        return new static('Stream is not seekable.');
    }

    public static function streamIsNotWritable(): static
    {
        return new static('Stream is not writable.');
    }

    public static function uploadDirIsNotWritable(): static
    {
        return new static('Upload directory is not writable.');
    }

    public static function streamMissingHasBeenMoved(): static
    {
        return new static('Stream is missing, it probably has been moved already.');
    }

    public static function unableToCreateStream(string $error): static
    {
        return new static(sprintf('Unable to create stream: %s.', $error));
    }

    public static function uploadedFileAlreadyMoved(): static
    {
        return new static('Uploaded file has already been moved.');
    }

    public static function fileError(string $error): static
    {
        return new static(sprintf('File error: %s.', $error));
    }
}
