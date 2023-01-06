<?php

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="api_tokens",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="token_hash_unique", columns={"token_hash"})
 *     },
 *     indexes={
 *         @ORM\Index(name="merchant_id_idx", columns={"merchant_id"})
 *     }
 * )
 */
class ApiToken extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $merchantId;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $tokenHash;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $expiresAt;

    /**
     * This field does not get stored in the database. Its only purpose is to
     * display the token in plaintext once it is created. After its creation,
     * the token will never be displayed in plaintext again.
     *
     * @var string|null
     */
    private $token = null;

    /**
     * @param int $merchantId
     * @param string $token
     * @param DateTime $expiresAt
     */
    public function __construct(
        int $merchantId,
        string $token,
        DateTime $expiresAt
    ) {
        $this->merchantId = $merchantId;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchantId;
    }

    /**
     * @param int $merchantId
     */
    public function setMerchantId(int $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return string
     */
    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    /**
     * @param string $tokenHash
     */
    public function setTokenHash(string $tokenHash): void
    {
        $this->tokenHash = $tokenHash;
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param DateTime $expiresAt
     */
    public function setExpiresAt(DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }
}
