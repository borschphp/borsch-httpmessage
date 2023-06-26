<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Exception;

use Psr\Http\Message\UploadedFileInterface;

/**
 * Class InvalidArgumentException
 */
class InvalidArgumentException extends \InvalidArgumentException
{

    public static function mustBeAString(string $name): static
    {
        return new static(sprintf('%s must be a non-empty string.', $name));
    }

    public static function mustBeAnInteger(string $name): static
    {
        return new static(sprintf('%s must be an integer.', $name));
    }

    public static function mustBeAStringOrAnArrayOfString(string $name): static
    {
        return new static(sprintf('%s must be a string or an array of strings.', $name));
    }

    public static function mustBeAStringOrAnInstanceOf(string $name, string $instance): static
    {
        return new static(sprintf('%s must be a string or an instance of %s.', $name, $instance));
    }

    public static function invalid(string $name): static
    {
        return new static(sprintf('Invalid %s.', $name));
    }

    public static function notFound(string $name): static
    {
        return new static(sprintf('%s not found.', $name));
    }

    public static function invalidUploadedFile(): static
    {
        return new static(sprintf(
            'Invalid value in uploaded files specification; must be an instance of %s.',
            UploadedFileInterface::class
        ));
    }

    public static function unableToParseUri(string $uri): static
    {
        return new static(sprintf('Unable to parse URI: %s.', $uri));
    }
}
