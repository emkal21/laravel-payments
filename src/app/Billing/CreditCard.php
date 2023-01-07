<?php

namespace App\Billing;

class CreditCard
{
    /** @var string $cardNumber */
    private $cardNumber;

    /** @var int $expirationMonth */
    private $expirationMonth;

    /** @var int $expirationYear */
    private $expirationYear;

    /** @var string $cvv */
    private $cvv;

    /** @var string $cardholderName */
    private $cardholderName;

    /**
     * @param string $cardNumber
     * @param int $expirationMonth
     * @param int $expirationYear
     * @param string $cvv
     * @param string $cardholderName
     */
    public function __construct(
        string $cardNumber,
        int $expirationMonth,
        int $expirationYear,
        string $cvv,
        string $cardholderName
    ) {
        $this->cardNumber = $cardNumber;
        $this->expirationMonth = $expirationMonth;
        $this->expirationYear = $expirationYear;
        $this->cvv = $cvv;
        $this->cardholderName = $cardholderName;
    }

    /**
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber(string $cardNumber): void
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return int
     */
    public function getExpirationMonth(): int
    {
        return $this->expirationMonth;
    }

    /**
     * @param int $expirationMonth
     */
    public function setExpirationMonth(int $expirationMonth): void
    {
        $this->expirationMonth = $expirationMonth;
    }

    /**
     * @return int
     */
    public function getExpirationYear(): int
    {
        return $this->expirationYear;
    }

    /**
     * @param int $expirationYear
     */
    public function setExpirationYear(int $expirationYear): void
    {
        $this->expirationYear = $expirationYear;
    }

    /**
     * @return string
     */
    public function getCvv(): string
    {
        return $this->cvv;
    }

    /**
     * @param string $cvv
     */
    public function setCvv(string $cvv): void
    {
        $this->cvv = $cvv;
    }

    /**
     * @return string
     */
    public function getCardholderName(): string
    {
        return $this->cardholderName;
    }

    /**
     * @param string $cardholderName
     */
    public function setCardholderName(string $cardholderName): void
    {
        $this->cardholderName = $cardholderName;
    }

    /**
     * @return string
     */
    public function getTwoDigitExpirationMonth(): string
    {
        return str_pad(
            (string)$this->getExpirationMonth(), 2, '0'
        );
    }

    /**
     * @param string $expirationDate
     * @return int[]
     */
    public static function splitExpirationDate(string $expirationDate): array
    {
        $expirationDateParts = explode('/', $expirationDate);
        $expirationMonth = intval($expirationDateParts[0]);
        $expirationYear = intval($expirationDateParts[1]);

        return [$expirationMonth, $expirationYear];
    }
}
