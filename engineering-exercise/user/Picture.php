<?php

class Picture
{
    private string $large;
    private string $medium;
    private string $thumbnail;

    /**
     * @param string $large
     * @param string $medium
     * @param string $thumbnail
     */
    public function __construct(string $large, string $medium, string $thumbnail)
    {
        $this->setLarge($large);
        $this->setMedium($medium);
        $this->setThumbnail($thumbnail);
    }

    /**
     * @return string
     */
    public function getLarge(): string
    {
        return $this->large;
    }

    /**
     * @param string $large
     */
    public function setLarge(string $large): void
    {
        $this->large = $large;
    }

    /**
     * @return string
     */
    public function getMedium(): string
    {
        return $this->medium;
    }

    /**
     * @param string $medium
     */
    public function setMedium(string $medium): void
    {
        $this->medium = $medium;
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }


}