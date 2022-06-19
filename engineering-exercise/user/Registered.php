<?php

class Registered
{
    private string $registered_at;
    private int $account_age;

    /**
     * @param string $registered_at
     * @param int $account_age
     */
    public function __construct(string $registered_at, int $account_age)
    {
        $this->setRegisteredAt($registered_at);
        $this->setAccountAge($account_age);
    }

    /**
     * @return string
     */
    public function getRegisteredAt(): string
    {
        return $this->registered_at;
    }

    /**
     * @param string $registered_at
     */
    public function setRegisteredAt(string $registered_at): void
    {
        $this->registered_at = $registered_at;
    }

    /**
     * @return int
     */
    public function getAccountAge(): int
    {
        return $this->account_age;
    }

    /**
     * @param int $account_age
     */
    public function setAccountAge(int $account_age): void
    {
        $this->account_age = $account_age;
    }


}