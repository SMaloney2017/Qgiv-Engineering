<?php

class Transaction
{
    private float $amount;
    private string $status;
    private string $payment_method;

    /**
     * @param float $amount
     * @param string $status
     * @param string $payment_method
     */
    public function __construct(float $amount, string $status, string $payment_method)
    {
        $this->setAmount($amount);
        $this->setStatus($status);
        $this->setPaymentMethod($payment_method);
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->payment_method;
    }

    /**
     * @param string $payment_method
     */
    public function setPaymentMethod(string $payment_method): void
    {
        $this->payment_method = $payment_method;
    }
}