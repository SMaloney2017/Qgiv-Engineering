<?php

class Timezone
{
    private string $offset;
    private string $description;

    /**
     * @param string $offset
     * @param string $description
     */
    public function __construct(string $offset, string $description)
    {
        $this->setOffset($offset);
        $this->setDescription($description);
    }

    /**
     * @return string
     */
    public function getOffset(): string
    {
        return $this->offset;
    }

    /**
     * @param string $offset
     */
    public function setOffset(string $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


}