<?php

namespace App\CodeLists;

use ReflectionClass;

abstract class AbstractCodeList
{
    /**
     * @return array
     */
    public static function all(): array
    {
        $reflectionClass = new ReflectionClass(static::class);

        $constants = $reflectionClass->getConstants();

        return array_values($constants);
    }

    /**
     * @param string $delimiter
     * @return string
     */
    public static function allAsString(string $delimiter = ','): string
    {
        return implode(static::all(), $delimiter);
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function valueExists(string $value): bool
    {
        return in_array($value, static::all());
    }
}
