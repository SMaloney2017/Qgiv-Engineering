<?php

require 'Name.php';
require 'Street.php';
require 'Coordinates.php';
require 'Location.php';
require 'Timezone.php';
require 'Dob.php';
require 'Registered.php';
require 'Picture.php';

class User
{
    private int $user_id;
    private string $gender;
    private Name $name;
    private Location $location;
    private string $email;
    private Dob $dob;
    private Registered $registered;
    private string $phone;
    private string $cell;
    private Picture $picture;
    private array $transactions = array();

    public function __construct(int $user_id, object $json)
    {
        $this->setUserId($user_id);

        $this->setGender($json->gender);

        $name = new Name($json->name->title, $json->name->first, $json->name->last);
        $this->setName($name);

        $street = new Street($json->location->street->name, $json->location->street->number);
        $coordinates = new Coordinates($json->location->coordinates->latitude, $json->location->coordinates->longitude);
        $timezone = new Timezone($json->location->timezone->offset, $json->location->timezone->description);
        $location = new Location(
            $street,
            $json->location->city,
            $json->location->state,
            $json->location->country,
            $json->location->postcode,
            $coordinates,
            $timezone
        );
        $this->setLocation($location);

        $this->setEmail($json->email);

        $dob = new Dob($json->dob->date, $json->dob->age);
        $this->setDob($dob);

        $registered = new Registered($json->registered->date, $json->registered->age);
        $this->setRegistered($registered);

        $this->setPhone($json->phone);

        $this->setCell($json->cell);

        $picture = new Picture($json->picture->large, $json->picture->medium, $json->picture->thumbnail);
        $this->setPicture($picture);
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Dob
     */
    public function getDob(): Dob
    {
        return $this->dob;
    }

    /**
     * @param Dob $dob
     */
    public function setDob(Dob $dob): void
    {
        $this->dob = $dob;
    }

    /**
     * @return Registered
     */
    public function getRegistered(): Registered
    {
        return $this->registered;
    }

    /**
     * @param Registered $registered
     */
    public function setRegistered(Registered $registered): void
    {
        $this->registered = $registered;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getCell(): string
    {
        return $this->cell;
    }

    /**
     * @param string $cell
     */
    public function setCell(string $cell): void
    {
        $this->cell = $cell;
    }

    /**
     * @return Picture
     */
    public function getPicture(): Picture
    {
        return $this->picture;
    }

    /**
     * @param Picture $picture
     */
    public function setPicture(Picture $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return array
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @param array $transactions
     */
    public function setTransactions(array $transactions): void
    {
        $this->transactions = $transactions;
    }

    /**
     * Append a new transaction to the User's transaction history.
     *
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction): void
    {
        $this->getTransactions()[] = $transaction;
    }
}