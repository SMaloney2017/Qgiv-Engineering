<?php

require './Database/Connection.php';
require 'GenerateHtmlMethods.php';

/* max rows for pagination */
const MAX_ROWS = 10;

const TRANSACTION_ATTRIBUTES = [
    'transaction_id',
    'timestamp',
    'amount',
    'status',
    'payment_method',
];

/* get user_id */
$user_id = '';
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    echo "No user_id selected.";
}

/* sort by attribute */
$sorting = 'transaction_id';
if (isset($_GET['sorting'])) {
    $sorting = $_GET['sorting'];
} else {
    $_GET['sorting'] = 'transaction_id';
}

/* sort ascending or descending */
$order = 'ASC';
if (isset($_GET['order'])) {
    $order = $_GET['order'];
} else {
    $_GET['order'] = 'ASC';
}

/* retrieve and store value from text-field */
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$page = 1;
$start = 0;
/* retrieve and store page number from navigation links, default to page 1 */
if (isset($_GET['page'])) {
    $page = max($_GET['page'], 1);
    $start = ($page - 1) * MAX_ROWS;
} else {
    $_GET['page'] = 1;
}

/* initialize page_count, row_count, and results as zero to avoid warnings */
$page_count = 0;
$row_count = 0;
$user_info = array();
$transaction_history = array();
try {
    /* create a connection to the Database */
    $connection = new Connection();
    $pdo = $connection->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* retrieve User information */
    $sql = 'SELECT users.user_id, users.email, users.phone, users.cell, users.registered_at, users.account_age,
                identification.gender, identification.title, identification.first, identification.last, identification.dob, identification.age,
                location.street, location.city, location.state, location.country, location.postcode, ST_X(location.coordinates) as latitude,  ST_Y(location.coordinates) as longitude, location.offset, location.description
            FROM users JOIN identification ON users.user_id = identification.user_id
                JOIN location ON users.user_id = location.user_id
            WHERE users.user_id = :id';

    $run = $pdo->prepare($sql);
    $data = [
        ':id' => $user_id
    ];

    $run->execute($data);
    $run->setFetchMode(PDO::FETCH_ASSOC);
    $user_info = $run->fetch();

    /* retrieve User transactions */
    $sql = 'SELECT * from transactions
            WHERE transactions.user_id = :id
                AND (transactions.transaction_id LIKE :search
                OR transactions.amount LIKE :search
                OR transactions.payment_method LIKE :search)';
    $run = $pdo->prepare($sql);
    $data = [
        ':id' => $user_id,
        ':search' => '%' . $search . '%',
    ];
    /* initial transactions query to get row_count & page_count */
    $run->execute($data);

    /* calculate number of pages */
    $row_count = $run->rowCount();
    $page_count = ceil($row_count / MAX_ROWS);

    /* append sorting and limit to sql-query */
    $limit = ' LIMIT ' . $start . ',' . MAX_ROWS;
    $ordering = ' ORDER BY ' . $sorting . ' ' . $order;
    $sql_limited = $sql . $ordering . $limit;

    /* execute paginated query and return associative array into $transaction_history */
    $run = $pdo->prepare($sql_limited);
    $run->execute($data);

    $run->setFetchMode(PDO::FETCH_ASSOC);
    $transaction_history = $run->fetchAll();

    /* close connection */
    $connection = null;
    $pdo = null;
} catch (PDOException $e) {
    /* log any errors from either query execution */
    error_log('Error: ' . $e->getMessage());
    //die()
}

?>

<!-- generate html -->
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<!-- we can reuse the header to share style across multiple pages -->
<?php
require 'GenerateHtmlHead.php' ?>

<body>
<section class="container">
    <div class="logo-header">
        <img class="logo" src="https://secure.qgiv.com/resources/core/images/logo-qgiv.svg" alt="Qgiv logo"/>
    </div>

    <form name='search' method='get'>
        <?php
        /* hidden form field to retain user_id on reload */
        echo "<input type='hidden' name='id' value=$user_id>";

        /* build tables displaying relevant User information */
        echo "<h3>User Information</h3>";
        buildUserInformation($user_info);

        /* build tables displaying User's transaction history */
        echo "<h3>Transaction History</h3>";

        /* build menu entries for sorting transaction */
        buildMenuEntries($sorting, TRANSACTION_ATTRIBUTES);

        /* build radio buttons for sorting (ascending / descending) */
        buildRadioButton("order", $order, "ASC");
        buildRadioButton("order", $order, "DESC");
        echo "<br><hr>";

        /* search text-field to narrow results by User-id, email, name, etc. */
        buildSearchField($search);

        /* builds 'Previous' and 'Next' buttons for paginated results */
        buildNavigation($page, $page_count, $search, $sorting, $order, $radio = 0, $user_id);
        echo "<br><hr>";

        buildInfoTable($transaction_history, TRANSACTION_ATTRIBUTES);
        ?>
    </form>
</section>
</body>
</html>
