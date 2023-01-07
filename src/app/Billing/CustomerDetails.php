<?php

namespace App\Billing;

class CustomerDetails
{
    /** @var string $email */
    private $email;

    /** @var string $addressLine1 */
    private $addressLine1;

    /** @var string $addressCity */
    private $addressCity;

    /** @var string $addressCountry Two-letter country code (ISO 3166-1 alpha-2) */
    private $addressCountry;

    /**
     * @param string $email
     * @param string $addressLine1
     * @param string $addressCity
     * @param string $addressCountry
     */
    public function __construct(
        string $email,
        string $addressLine1,
        string $addressCity,
        string $addressCountry
    ) {
        $this->email = $email;
        $this->addressLine1 = $addressLine1;
        $this->addressCity = $addressCity;
        $this->addressCountry = $addressCountry;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     */
    public function setAddressLine1(string $addressLine1): void
    {
        $this->addressLine1 = $addressLine1;
    }

    /**
     * @return string
     */
    public function getAddressCity(): string
    {
        return $this->addressCity;
    }

    /**
     * @param string $addressCity
     */
    public function setAddressCity(string $addressCity): void
    {
        $this->addressCity = $addressCity;
    }

    /**
     * @return string
     */
    public function getAddressCountry(): string
    {
        return $this->addressCountry;
    }

    /**
     * @param string $addressCountry
     */
    public function setAddressCountry(string $addressCountry): void
    {
        $this->addressCountry = $addressCountry;
    }
}
