<?php

class Name
{
    private string $title;
    private string $first;
    private string $last;

    /**
     * @param string $title
     * @param string $first
     * @param string $last
     */
    public function __construct(string $title, string $first, string $last)
    {
        $this->setTitle($title);
        $this->setFirst($first);
        $this->setLast($last);
    }

    /**
     * Returns the full title and name of the user. E.g., `Mr Sean Maloney`
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getTitle() . " " . $this->getFirst() . " " . $this->getLast();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFirst(): string
    {
        return $this->first;
    }

    /**
     * @param string $first
     */
    public function setFirst(string $first): void
    {
        $this->first = $first;
    }

    /**
     * @return string
     */
    public function getLast(): string
    {
        return $this->last;
    }

    /**
     * @param string $last
     */
    public function setLast(string $last): void
    {
        $this->last = $last;
    }
}