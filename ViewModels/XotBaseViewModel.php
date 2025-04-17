<?php
/**
 * @see https://martinjoo.dev/how-to-use-data-transfer-objects-and-actions-in-laravel
 */

declare(strict_types=1);

namespace Modules\Xot\ViewModels;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

abstract class XotBaseViewModel implements Arrayable
{
    /**
     * Undocumented function.
     */
    public function toArray(): array
    {
        return collect((new \ReflectionClass($this))->getMethods())
            ->reject(
                fn (\ReflectionMethod $method) => \in_array($method->getName(), ['__construct', 'toArray'], true)
            )
            ->filter(
                fn (\ReflectionMethod $method) => \in_array(
                    'public',
                    \Reflection::getModifierNames($method->getModifiers()),
                    true
                )
            )
            ->mapWithKeys(fn (\ReflectionMethod $method) => [
                Str::snake($method->getName()) => $this->{$method->getName()}(),
            ])
            ->toArray();
    }
}
