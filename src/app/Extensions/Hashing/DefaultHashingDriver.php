<?php

namespace App\Extensions\Hashing;

use Illuminate\Support\Facades\Hash;

class DefaultHashingDriver implements HashingDriverInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function make(string $value): string
    {
        return Hash::make($value);
    }

    /**
     * @param string $plaintextValue
     * @param string $hashedValue
     * @return bool
     */
    public function verify(string $plaintextValue, string $hashedValue): bool
    {
        return Hash::check($plaintextValue, $hashedValue);
    }
}
