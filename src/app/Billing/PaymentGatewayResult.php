<?php

namespace App\Billing;

class PaymentGatewayResult
{
    /** @var bool $isSuccess */
    private $isSuccess;

    /** @var string|null $errorMessage */
    private $errorMessage;

    /** @var bool $isFurtherActionRequired */
    private $isFurtherActionRequired;

    /** @var string|null $furtherActionUrl */
    private $furtherActionUrl;

    /**
     * @param bool $isSuccess
     * @param string|null $errorMessage
     * @param bool $isFurtherActionRequired
     * @param string|null $furtherActionUrl
     */
    public function __construct(
        bool $isSuccess = false,
        ?string $errorMessage = null,
        bool $isFurtherActionRequired = false,
        ?string $furtherActionUrl = null
    ) {
        $this->isSuccess = $isSuccess;
        $this->errorMessage = $errorMessage;
        $this->isFurtherActionRequired = $isFurtherActionRequired;
        $this->furtherActionUrl = $furtherActionUrl;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * @param bool $isSuccess
     */
    public function setIsSuccess(bool $isSuccess): void
    {
        $this->isSuccess = $isSuccess;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param string|null $errorMessage
     */
    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return bool
     */
    public function isFurtherActionRequired(): bool
    {
        return $this->isFurtherActionRequired;
    }

    /**
     * @param bool $isFurtherActionRequired
     */
    public function setIsFurtherActionRequired(bool $isFurtherActionRequired
    ): void {
        $this->isFurtherActionRequired = $isFurtherActionRequired;
    }

    /**
     * @return string|null
     */
    public function getFurtherActionUrl(): ?string
    {
        return $this->furtherActionUrl;
    }

    /**
     * @param string|null $furtherActionUrl
     */
    public function setFurtherActionUrl(?string $furtherActionUrl): void
    {
        $this->furtherActionUrl = $furtherActionUrl;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'isSuccess' => $this->isSuccess,
            'errorMessage' => $this->errorMessage,
            'isFurtherActionRequired' => $this->isFurtherActionRequired,
            'furtherActionUrl' => $this->furtherActionUrl,
        ];
    }
}
