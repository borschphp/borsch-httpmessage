<?php

namespace Borsch\Http;

use Borsch\Http\Exception\InvalidArgumentException;

final class Header
{

    /** @var string|string[] $values */
    public readonly string|array $values;
    public readonly string $normalized_name;

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

        $is_all_strings = array_reduce(
            $values,
            fn ($carry, $value) => $carry && is_string($value) && strlen($value) > 0,
            true
        );

        if (!$is_all_strings) {
            throw InvalidArgumentException::mustBeAStringOrAnArrayOfString('Header value');
        }

        $this->values = $values;

        $this->normalized_name = strtolower($this->name);
    }

    public function __toString():string
    {
        return sprintf('%s: %s', $this->name, implode(', ', $this->values));
    }

    public function equals(Header $header): bool
    {
        return $this->normalized_name == $header->normalized_name &&
            $this->values == $header->values;
    }
}