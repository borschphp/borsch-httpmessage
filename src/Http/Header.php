<?php

namespace Borsch\Http;

use Borsch\Http\Exception\InvalidArgumentException;
use function is_string, strlen, strtolower, sprintf, implode;

final class Header
{

    /** @var string|string[] $values */
    public readonly string|array $values;

    public function __construct(
        public readonly string $name,
        string|array $values
    ) {
        if (strlen($this->name) == 0) {
            throw InvalidArgumentException::mustBeAString('Header name');
        }

        if (is_string($values)) {
            $values = [$values];
        }

        if (empty($values)) {
            throw InvalidArgumentException::mustBeAStringOrAnArrayOfString('Header value(s)');
        }

        $is_all_strings = array_reduce(
            $values,
            fn ($carry, $value) => $carry && is_string($value),
            true
        );

        if (!$is_all_strings) {
            throw InvalidArgumentException::mustBeAStringOrAnArrayOfString('Header value');
        }

        $this->values = array_values($values);
    }

    public function __toString():string
    {
        return sprintf('%s: %s', $this->name, implode(', ', $this->values));
    }

    public function equals(Header $header): bool
    {
        return strtolower($this->name) === strtolower($header->name) &&
            $this->values == $header->values;
    }
}