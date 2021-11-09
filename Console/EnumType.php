<?php

declare(strict_types=1);

namespace Modules\Xot\Console;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumType extends Type
{
    public const ENUM = 'enum';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return self::ENUM;
    }

    public function getName(): string
    {
        return self::ENUM;
    }
}