<?php

declare(strict_types=1);

namespace Modules\Xot\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Modules\Xot\ValueObjects\PhoneValueObject;

class PhoneCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     * @param mixed                $model
     *                                         // Parameter #1 $model (Illuminate\Database\Eloquent\Model) of method Modules\Xot\Casts\PhoneCast::get()
     *                                         // is not contravariant with parameter #1 $model (mixed) of method
     *                                         //    Illuminate\Contracts\Database\Eloquent\CastsAttributes::get()
     */
    public function get($model, string $key, mixed $value, array $attributes): PhoneValueObject
    {
        if (! is_string($value)) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }

        return PhoneValueObject::fromString($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     * @param mixed                $model
     *                                         //Parameter #1 $model (Illuminate\Database\Eloquent\Model) of method Modules\Xot\Casts\PhoneCast::set()
     *                                         // is not contravariant with parameter #1 $model (mixed) of method
     *                                         // Illuminate\Contracts\Database\Eloquent\CastsAttributes::set()
     */
    public function set($model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof PhoneValueObject) {
            throw new \InvalidArgumentException('The given value is not an Phone instance.');
        }

        return $value->toString();
    }
}
