<?php

require 'GenerateHtmlMethods.php';
require './Database/Connection.php';

/* max rows for pagination */
const MAX_ROWS = 15;

/* attributes for dynamically building menu's and headers,
   as well as verifying that the selected ordering is valid */
const USER_ATTRIBUTES_BASIC = [
    'user_id',
    'email',
    'first',
    'last'
];

const TRANSACTION_ATTRIBUTES = [
    'transaction_id',
    'user_id',
    'timestamp',
    'amount',
    'status',
    'payment_method',
];

/* selected table - transactions or users */
$radio = 'users';
if (isset($_GET['radio'])) {
    $radio = $_GET['radio'];
} else {
    $_GET['radio'] = 'users';
}

/* sort by attribute */
$sorting = 'user_id';
if (isset($_GET['sorting'])) {
    $sorting = $_GET['sorting'];
} else {
    $_GET['sorting'] = 'user_id';
}

/* sort ascending or descending */
$order = 'ASC';
if (isset($_GET['order'])) {
    $order = $_GET['order'];
} else {
    $_GET['order'] = 'ASC';
}

/* verify sorting is a valid attribute to the selected table ->
   if sorting is NOT an attribute in the selected table,
   default to a common value: user_id */
if (!in_array($sorting, $radio == 'users' ? USER_ATTRIBUTES_BASIC : TRANSACTION_ATTRIBUTES)) {
    $sorting = 'user_id';
    $_GET['sorting'] = 'user_id';
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
$results = array();
try {
    /* create a connection to the Database */
    $connection = new Connection();
    $pdo = $connection->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* select attributes where search-value is like any of the attributes,
       query either transactions or users depending on selected radio-button */
    $sql = $radio == 'users' ?
        'SELECT users.user_id, users.email, identification.first, identification.last
            FROM users JOIN identification ON users.user_id = identification.user_id
            WHERE users.user_id LIKE :search OR users.email LIKE :search OR identification.first LIKE :search OR identification.last LIKE :search'
        : 'SELECT transactions.transaction_id, transactions.user_id, transactions.timestamp, transactions.amount, transactions.status, transactions.payment_method
            FROM transactions
            WHERE transactions.user_id LIKE :search OR transactions.transaction_id LIKE :search';

    $run = $pdo->prepare($sql);
    $data = [
        ':search' => '%' . $search . '%',
    ];
    /* initial query to get row_count & page_count */
    $run->execute($data);

    /* calculate number of pages */
    $row_count = $run->rowCount();
    $page_count = ceil($row_count / MAX_ROWS);

    /* append sorting and limit to sql-query */
    $limit = ' LIMIT ' . $start . ',' . MAX_ROWS;
    $ordering = ' ORDER BY ' . $sorting . ' ' . $order;
    $sql_limited = $sql . $ordering . $limit;

    /* execute paginated query and return associative array into $results */
    $run = $pdo->prepare($sql_limited);
    $run->execute($data);
    $run->setFetchMode(PDO::FETCH_ASSOC);
    $results = $run->fetchAll();

    /* close connection */
    $connection = null;
    $pdo = null;
} catch (PDOException $e) {
    /* log any errors from either query execution */
    error_log('Error: ' . $e->getMessage());
    //die()
}
?>

<!-- begin generating html -->
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
<form name='search' method='get'>
    <?php
    /* build radio buttons to select view (transactions / users) */
    buildRadioButton("radio", $radio, "transactions");
    buildRadioButton("radio", $radio, "users");
    echo "<br><hr>";

    /* if users radio button selected, build User menu-entries
       otherwise, build transaction menu-entries */
    if ($radio == 'users') {
        buildMenuEntries($sorting, USER_ATTRIBUTES_BASIC);
    } else {
        buildMenuEntries($sorting, TRANSACTION_ATTRIBUTES);
    }

    /* build radio buttons for sorting (ascending / descending) */
    buildRadioButton("order", $order, "ASC");
    buildRadioButton("order", $order, "DESC");
    echo "<br><hr>";

    /* search text-field to narrow results by User-id, email, name, etc. */
    buildSearchField($search);

    /* builds 'Previous' and 'Next' buttons for paginated results */
    buildNavigation($page, $page_count, $radio, $search, $sorting, $order);
    echo "<br><hr>";

    /* build selected table */
    if ($radio == 'users') {
        buildInfoTable($results, USER_ATTRIBUTES_BASIC);
    } else {
        buildInfoTable($results, TRANSACTION_ATTRIBUTES);
    }
    ?>
</form>
</body>
</html>
