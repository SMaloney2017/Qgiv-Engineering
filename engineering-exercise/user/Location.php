<?php

class Location
{
    private Street $street;
    private string $city;
    private string $state;
    private string $country;
    private string $postcode;
    private Coordinates $coordinates;
    private Timezone $timezone;

    /**
     * @param Street $street
     * @param string $city
     * @param string $state
     * @param string $country
     * @param string $postcode
     * @param Coordinates $coordinates
     * @param Timezone $timezone
     */
    public function __construct(
        Street $street,
        string $city,
        string $state,
        string $country,
        string $postcode,
        Coordinates $coordinates,
        Timezone $timezone
    ) {
        $this->setStreet($street);
        $this->setCity($city);
        $this->setState($state);
        $this->setCountry($country);
        $this->setPostcode($postcode);
        $this->setCoordinates($coordinates);
        $this->setTimezone($timezone);
    }

    /**
     * @return Street
     */
    public function getStreet(): Street
    {
        return $this->street;
    }

    /**
     * @param Street $street
     */
    public function setStreet(Street $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * @return Coordinates
     */
    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @param Coordinates $coordinates
     */
    public function setCoordinates(Coordinates $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return Timezone
     */
    public function getTimezone(): Timezone
    {
        return $this->timezone;
    }

    /**
     * @param Timezone $timezone
     */
    public function setTimezone(Timezone $timezone): void
    {
        $this->timezone = $timezone;
    }

}