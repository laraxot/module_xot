<?php

declare(strict_types=1);
/**
 * @see https://medium.com/@sliusarchyn/value-objects-in-laravel-use-it-12ba71b00281
 */

namespace Modules\Xot\ValueObjects;

use function Safe\preg_match;

class PhoneValueObject
{
    private function __construct(private readonly string $phone)
    {
    }

    public static function fromString(string $phone): PhoneValueObject
    {
        if (! preg_match('/^\+1\d{10}$/', $phone)) {
            throw new \InvalidArgumentException('It is not valid phone value');
        }

        return new self($phone);
    }

    public function toString(): string
    {
        return $this->phone;
    }
}
