<?php

namespace App\Extensions\Hashing;

interface HashingDriverInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function make(string $value): string;

    /**
     * @param string $plaintextValue
     * @param string $hashedValue
     * @return bool
     */
    public function verify(string $plaintextValue, string $hashedValue): bool;
}
