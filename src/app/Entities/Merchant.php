<?php

namespace App\Entities;

use App\CodeLists\PaymentServiceProviders;
use App\Exceptions\IllegalArgumentException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="merchants",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="username_unique", columns={"username"})
 *     },
 * )
 */
class Merchant extends AbstractEntity
{
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $preferredPaymentService;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $paymentServiceSecretKey;

    /**
     * @param string $name
     * @param string $username
     * @param string $preferredPaymentService
     * @param string $paymentServiceSecretKey
     * @throws IllegalArgumentException
     */
    public function __construct(
        string $name,
        string $username,
        string $preferredPaymentService,
        string $paymentServiceSecretKey
    ) {
        $this->checkPreferredPaymentService($preferredPaymentService);

        $this->name = $name;
        $this->username = $username;
        $this->preferredPaymentService = $preferredPaymentService;
        $this->paymentServiceSecretKey = $paymentServiceSecretKey;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPreferredPaymentService(): string
    {
        return $this->preferredPaymentService;
    }

    /**
     * @param string $preferredPaymentService
     * @throws IllegalArgumentException
     */
    public function setPreferredPaymentService(string $preferredPaymentService
    ): void {
        $this->checkPreferredPaymentService($preferredPaymentService);

        $this->preferredPaymentService = $preferredPaymentService;
    }

    /**
     * @return string
     */
    public function getPaymentServiceSecretKey(): string
    {
        return $this->paymentServiceSecretKey;
    }

    /**
     * @param string $paymentServiceSecretKey
     */
    public function setPaymentServiceSecretKey(string $paymentServiceSecretKey
    ): void {
        $this->paymentServiceSecretKey = $paymentServiceSecretKey;
    }

    /**
     * @param string $preferredPaymentService
     * @return void
     * @throws IllegalArgumentException
     */
    private function checkPreferredPaymentService(
        string $preferredPaymentService
    ): void {
        if (!PaymentServiceProviders::valueExists($preferredPaymentService)) {
            $values = PaymentServiceProviders::allAsString();

            $message = sprintf('Merchant preferred payment service must be one of: %s',
                $values);

            throw new IllegalArgumentException($message);
        }
    }
}
