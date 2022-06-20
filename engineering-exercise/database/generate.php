<?php
/*  a script for populating the database with users & randomly generated transactions */

include './database/Connection.php';
include './user/User.php';
include './user/Transaction.php';

const STATUS = array('Accepted', 'Declined');
const PAYMENTS = array('Visa', 'Mastercard', 'Discover', 'American Express', 'eCheck');
const URL = 'https://randomuser.me/api/?results=500&nat=us&exc=login,id,nat';

/* create a connection to our database */
$connection = new Connection();

/* fetch users from randomuser.me api and decode into an object */
$json_obj = json_decode(file_get_contents(URL));
$json_results = $json_obj->results;

/* for each user in the array of results */
for ($i = 0; $i < count($json_results); $i++) {
    /* create a new user object */
    $user = new User($i, $json_results[$i]);

    /* insert user into the database */
    $connection->insertUserIntoDatabase($user);

    /* randomly generate between 1 and 5 transactions for each user,
       insert transactions into database */
    $n = mt_rand(1, 5);
    for ($j = 0; $j < $n; $j++) {
        $parameters = generateRandomTransactionParameters();
        $transaction = new Transaction($parameters[0], $parameters[1], $parameters[2]);
        $user->addTransaction($transaction);
        $connection->insertIntoTableTransactions($i, $transaction);
    }
}

/**
 * Generate a random float with two decimal places to act as a transaction amount.
 *
 * @param int $min
 * @param int $max
 * @param int $decimals
 * @return float
 */
function random_float(int $min, int $max, int $decimals = 0): float
{
    $scale = pow(10, $decimals);
    return mt_rand($min * $scale, $max * $scale) / $scale;
}

/**
 * Generates the parameters for phony transactions to be added into the database
 * for validation and testing purposes.
 *
 * @return array
 */
function generateRandomTransactionParameters(): array
{
    $parameters = array();

    $amount = random_float(1, 100, 2);
    $parameters[] = $amount;

    $status = STATUS[mt_rand(0, 1)];
    $parameters[] = $status;

    $payment_method = PAYMENTS[mt_rand(0, 4)];
    $parameters[] = $payment_method;

    return $parameters;
}
