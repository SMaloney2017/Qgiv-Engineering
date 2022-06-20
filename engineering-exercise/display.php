<?php

const MAX_ROWS = 10;
require './database/Connection.php';
require './user/User.php';

/* selected table - transactions or users */
$radio = 'users';
if (isset($_GET['radio'])) {
    $search = $_GET['radio'];
} else {
    $_GET['radio'] = 'users';
}

/* retrieve and store value from text-field */
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$page = 1; // current page
$start = 0; // starting entry-value for sql query

/* retrieve and store page number from navigation links, default to page 1 */
if (isset($_GET['page'])) {
    $page = max($_GET['page'], 1);
    $start = ($page - 1) * MAX_ROWS;
} else {
    $_GET['page'] = 1;
}

/* initialize page_count and row_count as zero to avoid warnings */
$page_count = 0;
$row_count = 0;

try {
    /* create a connection to the database */
    $connection = new Connection();
    $pdo = $connection->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* select attributes where search-value is like any of the attributes,
       query either transactions or users depending on selected radio-button */
    $sql = $_GET['radio'] == 'users' ? 'SELECT users.user_id, users.email, identification.first, identification.last FROM users JOIN identification ON users.user_id = identification.user_id WHERE users.user_id LIKE :search OR users.email LIKE :search OR identification.first LIKE :search OR identification.last LIKE :search'
        : 'SELECT transactions.transaction_id, transactions.user_id, transactions.timestamp, transactions.amount, transactions.status, transactions.payment_method FROM transactions WHERE transactions.user_id LIKE :search OR transactions.transaction_id LIKE :search';
    $run = $pdo->prepare($sql);
    $data = [
        ':search' => '%' . $search . '%'
    ];
    /* initial query to get row_count & page_count */
    $run->execute($data);

    /* calculate number of pages */
    $row_count = $run->rowCount();
    $page_count = ceil($row_count / MAX_ROWS);

    /* append pagination limit to sql-query */
    $limit = ' LIMIT ' . $start . ',' . MAX_ROWS;
    $sql_limited = $sql . $limit;

    /* execute paginated query and return associative array into $results */
    $run = $pdo->prepare($sql_limited);
    $run->execute($data);
    $run->setFetchMode(PDO::FETCH_ASSOC);
    $results = $run->fetchAll();
} catch (PDOException $e) {
    /* log any errors from either query execution */
    error_log('Error: ' . $e->getMessage());
    //die()
}
?>

<!-- generate user-table html -->
<html lang='en'>
<body>
<form name='search' action='' method='get'>
    <!-- radio buttons to switch between transactions and users. -->
    <label>
        <input type="radio" name="radio" <?php
        if ($_GET['radio'] == "transactions") {
            echo "checked";
        } ?> value="transactions">
    </label>Transactions

    <label>
        <input type="radio" name="radio" <?php
        if ($_GET['radio'] == "users") {
            echo "checked";
        } ?> value="users">
    </label>User

    <!-- search text-field to narrow results by user-id, email, name, etc. -->
    <label for='search'></label>
    Search:
    <input type='text' name='search' value='<?php echo $search; ?>' id='search'>
    <input type='submit' onclick="window.location.reload();">

    <!-- this php block builds 'Previous' and 'Next' buttons for paginated results -->
    <?php
    /* get base-url path */
    $base_url = parse_url($_SERVER['REQUEST_URI'])['path'];

    /* if not viewing the first page of results */
    if ($page > 1) {
        $query = [
            'radio' => $_GET['radio'],
            'page' => $_GET['page'] - 1,
            'search' => $_GET['search']
        ];
        /* build a new url retaining query (search-value and decremented page-number) */
        $url = $base_url . "?" . http_build_query($query);
        /* link to previous page of results */
        echo "<a href='$url'>Previous</a>\t";
    }

    /* if there are remaining pages to view */
    if ($page < $page_count) {
        $query = [
            'radio' => $_GET['radio'],
            'page' => $_GET['page'] + 1,
            'search' => $_GET['search']
        ];
        /* build a new url retaining query (search-value and incrementing page-number) */
        $url = $base_url . "?" . http_build_query($query);
        /* link to next page of results */
        echo "<a href='$url'>Next</a>";
    }
    ?>

    <!-- begin building table which displays users -->
    <table class='data-table'>
        <thead>
        <!-- build table headers for either users or transactions -->
        <?php
        if ($_GET['radio'] == 'users') { ?>
            <tr class='ui-secondary-color'>
                <th class='ui-secondary-color'>Id</th>
                <th class='ui-secondary-color'>Email</th>
                <th class='ui-secondary-color'>First</th>
                <th class='ui-secondary-color'>Last</th>
            </tr>
        <?php
        } else { ?>
            <tr class='ui-secondary-color'>
                <th class='ui-secondary-color'>Transaction-Id</th>
                <th class='ui-secondary-color'>User-Id</th>
                <th class='ui-secondary-color'>Timestamp</th>
                <th class='ui-secondary-color'>Amount</th>
                <th class='ui-secondary-color'>Status</th>
                <th class='ui-secondary-color'>Payment Type</th>
            </tr>
        <?php
        } ?>

        </thead>
        <tbody>

        <?php
        /* given results is not empty */
        if (!empty($results)) {
            /* for each item */
            foreach ($results as $row) {
                /* if users radio-button selected */
                if ($_GET['radio'] == 'users') { ?>
                    <!-- build a new table-row displaying user-id, email,
                         first name, and last name -->
                    <tr class='data-row'>
                        <td><?php echo $row['user_id'] ?></td>
                        <td><?php echo $row['email'] ?></td>
                        <td><?php echo $row['first'] ?></td>
                        <td><?php echo $row['last'] ?></td>
                    </tr>
                    <!-- if transactions radio-button selected -->
                <?php
                } else { ?>
                    <!-- build a new table-row displaying transaction-id, user-id,
                         timestamp, amount, status, and payment method -->
                    <tr class='data-row'>
                        <td><?php echo $row['transaction_id'] ?></td>
                        <td><?php echo $row['user_id'] ?></td>
                        <td><?php echo $row['timestamp'] ?></td>
                        <td><?php echo $row['amount'] ?></td>
                        <td><?php echo $row['status'] ?></td>
                        <td><?php  echo $row['payment_method'] ?></td>
                    </tr>
                    <?php
                }
            }
        }
        ?>

        </tbody>
    </table>
</form>
</body>
</html>
