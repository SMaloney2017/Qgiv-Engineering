<?php

class Dob
{
    private string $date;
    private int $age;

    /**
     * @param string $date
     * @param int $age
     */
    public function __construct(string $date, int $age)
    {
        $this->setDate($date);
        $this->setAge($age);
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Return a formatted date-of-birth, E.g., `October 11, 1998`
     *
     * @return string
     */
    public function getFormattedDate(): string
    {
        return date('F j, Y', $this->date);
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }
}