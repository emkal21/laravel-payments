<?php

namespace App\Services;

use App\Extensions\Hashing\HashingDriverInterface;

class HashingService
{
    /** @var HashingDriverInterface $hashingDriver */
    private $hashingDriver;

    /**
     * @param HashingDriverInterface $hashingDriver
     */
    public function __construct(HashingDriverInterface $hashingDriver)
    {
        $this->hashingDriver = $hashingDriver;
    }

    /**
     * @param string $value
     * @return string
     */
    public function make(string $value): string
    {
        return $this->hashingDriver->make($value);
    }

    /**
     * @param string $plaintextValue
     * @param string $hashedValue
     * @return bool
     */
    public function verify(string $plaintextValue, string $hashedValue): bool
    {
        return $this->hashingDriver->verify($plaintextValue, $hashedValue);
    }
}
