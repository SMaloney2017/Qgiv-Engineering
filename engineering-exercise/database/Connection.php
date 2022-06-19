<?php
/*  creates a connection to the database. To be used in
    conjunction with the User classes to populate and query the database. */

class Connection
{
    private pdo $connection;

    public function __construct()
    {
        $config = require('config.php');
        $this->connect($config);
    }

    /**
     * @return pdo
     */
    public function getConnection(): pdo
    {
        return $this->connection;
    }

    /**
     * @param pdo $connection
     */
    public function setConnection(pdo $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Connect to a database as described by config.php
     *
     * @param array $config
     */
    public function connect(array $config): void
    {
        try {
            $connection = new PDO(
                'mysql:host=' . $config['host'] . '; dbname=' . $config['db'] . '; port=' . $config['port'],
                $config['username'],
                $config['password']
            );
            $this->setConnection($connection);
        } catch (PDOException $e) {
            error_log('Error: ' . $e->getMessage());
            //die();
        }
    }

    /**
     * Insert User object members into their respective tables.
     *
     * @param User $user
     */
    public function insertUserIntoDatabase(User $user): void
    {
        $this->insertIntoTableUsers(
            $user->getUserId(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getCell(),
            $user->getRegistered(),
        );

        $this->insertIntoTablePicture(
            $user->getUserId(),
            $user->getPicture()
        );

        $this->insertIntoTableLocation(
            $user->getUserId(),
            $user->getLocation()
        );

        $this->insertIntoTableIdentification(
            $user->getUserId(),
            $user->getGender(),
            $user->getName(),
            $user->getDob(),
        );
    }

    /**
     * Insert parameters into the `users` table.
     *
     * @param int $user_id
     * @param string $email
     * @param string $phone
     * @param string $cell
     * @param Registered $registered
     */
    private function insertIntoTableUsers(
        int $user_id,
        string $email,
        string $phone,
        string $cell,
        Registered $registered
    ): void {
        $sql = 'INSERT INTO `users`(`user_id`, `email`, `phone`, `cell`, `registered_at`, `account_age`) VALUES (:user_id, :email, :phone, :cell, :registered_at, :account_age)';
        $run = $this->getConnection()->prepare($sql);

        $data = [
            ':user_id' => $user_id,
            ':email' => $email,
            ':phone' => $phone,
            ':cell' => $cell,
            ':registered_at' => date('Y-m-d H:i:s', strtotime($registered->getRegisteredAt())),
            ':account_age' => $registered->getAccountAge(),
        ];

        try {
            $run->execute($data);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            //die();
        }
    }

    /**
     * Insert parameters into the `picture` table.
     *
     * @param int $user_id
     * @param Picture $picture
     */
    private function insertIntoTablePicture(int $user_id, Picture $picture): void
    {
        $sql = 'INSERT INTO `picture`(`user_id`, `large`, `medium`, `thumbnail`) VALUES (:user_id, :large, :medium, :thumbnail)';
        $run = $this->getConnection()->prepare($sql);

        $data = [
            ':user_id' => $user_id,
            ':large' => $picture->getLarge(),
            ':medium' => $picture->getMedium(),
            ':thumbnail' => $picture->getThumbnail()
        ];

        try {
            $run->execute($data);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            //die();
        }
    }

    /**
     * Insert parameters into the `location` table.
     *
     * @param int $user_id
     * @param Location $location
     */
    private function insertIntoTableLocation(int $user_id, Location $location): void
    {
        $sql = 'INSERT INTO `location`(`user_id`, `street`, `city`, `state`, `country`, `postcode`, `coordinates`, `offset`, `description`) VALUES (:user_id, :street, :city, :state, :country, :postcode, POINTFROMTEXT(:coordinates), :offset, :description)';
        $run = $this->getConnection()->prepare($sql);

        $data = [
            ':user_id' => $user_id,
            ':street' => $location->getStreet()->getStreetAddress(),
            ':city' => $location->getCity(),
            ':state' => $location->getState(),
            ':country' => $location->getCountry(),
            ':postcode' => $location->getPostcode(),
            ':coordinates' => 'POINT(' . $location->getCoordinates()->getLatitude() . ' ' . $location->getCoordinates(
                )->getLongitude() . ')',
            ':offset' => $location->getTimezone()->getOffset(),
            ':description' => $location->getTimezone()->getDescription()
        ];

        try {
            $run->execute($data);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            //die();
        }
    }

    /**
     * Insert parameters into the `identification` table.
     *
     * @param int $user_id
     * @param string $gender
     * @param Name $name
     * @param Dob $dob
     * @return void
     */
    private function insertIntoTableIdentification(int $user_id, string $gender, Name $name, Dob $dob): void
    {
        $sql = 'INSERT INTO `identification`(`user_id`, `gender`, `title`, `first`, `last`, `dob`, `age`) VALUES (:user_id, :gender, :title, :first, :last, :dob, :age)';
        $run = $this->getConnection()->prepare($sql);

        $data = [
            ':user_id' => $user_id,
            ':gender' => $gender,
            ':title' => $name->getTitle(),
            ':first' => $name->getFirst(),
            ':last' => $name->getLast(),
            ':dob' => date('Y-m-d H:i:s', strtotime($dob->getDate())),
            ':age' => $dob->getAge(),
        ];

        try {
            $run->execute($data);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            //die();
        }
    }

    /**
     * Insert a transaction into the `transaction` table.
     *
     * @param int $user_id
     * @param Transaction $transaction
     * @return void
     */
    public function insertIntoTableTransactions(int $user_id, Transaction $transaction): void
    {
        $sql = 'INSERT INTO `transactions`(`user_id`, `amount`, `status`, `payment_method`) VALUES (:user_id, :amount, :status, :payment_method)';
        $run = $this->getConnection()->prepare($sql);

        $data = [
            ':user_id' => $user_id,
            ':amount' => $transaction->getAmount(),
            ':status' => $transaction->getStatus(),
            ':payment_method' => $transaction->getPaymentMethod(),
        ];

        try {
            $run->execute($data);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            //die();
        }
    }
}