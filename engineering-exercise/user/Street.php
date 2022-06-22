<?php

class Street
{
    private string $name;
    private int $number;

    /**
     * @param string $name
     * @param int $number
     */
    public function __construct(string $name, int $number)
    {
        $this->setName($name);
        $this->setNumber($number);
    }

    /**
     * @return string
     */
    public function getStreetAddress(): string
    {
        return $this->getNumber() . ' ' . $this->getName();
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
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
}
