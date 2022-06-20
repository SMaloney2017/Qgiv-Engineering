<?php

const MAX_ROWS = 10;
require './database/Connection.php';
require './user/User.php';

/* attributes for dynamically building menu's and headers,
   as well as verifying that the selected ordering is valid */
$user_attributes = [
    'user_id',
    'email',
    'first',
    'last'
];

$transaction_attributes = [
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

/* verify $sorting is a valid attribute to the selected table ->
   if sorting is NOT an attribute in the selected table,
   default to a common value: user_id */
if(!in_array($sorting, $radio == 'users' ? $user_attributes : $transaction_attributes)) {
    $sorting = 'user_id';
    $_GET['sorting'] = 'user_id';
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

    /* sort results */


    /* close connection */
    $connection = null;
    $pdo = null;
} catch (PDOException $e) {
    echo $e;
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
require 'htmlhead.php' ?>

<body>
<form name='search' method='get'>

    <!-- radio buttons to switch between transactions and users. -->
    <label>
        <input type="radio" name="radio" onclick="" <?php
        if ($radio == "transactions") {
            echo "checked";
        } ?> value="transactions">
    </label>Transactions
    <label>
        <input type="radio" name="radio" <?php
        if ($radio == "users") {
            echo "checked";
        } ?> value="users">
    </label>Users
    <br/>

    <!-- menu for sorting -->
    <?php
    /* if users radio button selected */
    if ($radio == 'users') {
        ?>
        <!-- show sorting options for users -->
        <label for="sorting">Sort by:</label>
        <select id="sorting" name="sorting">
            <?php
            foreach ($user_attributes as $a) { ?>
                <option value="<?php echo "$a"; ?>"
                    <?php
                    if ($sorting == $a) {
                        echo "selected";
                    } ?>
                >
                <?php echo "$a"; ?></option><?php
            } ?>
        </select>
        <?php
        /* if transactions radio button selected */
    } else { ?>
        <!-- show sorting options for users -->
        <label for="sorting">Sort by:</label>
        <select id="sorting" name="sorting">
            <?php
            foreach ($transaction_attributes as $a) { ?>
                <option value="<?php echo "$a"; ?>"
                    <?php
                    if ($sorting == $a) {
                        echo "selected";
                    } ?>
                >
                <?php echo "$a"; ?></option><?php
            } ?>
        </select>
        <?php
    } ?>
    <!-- radio buttons for sorting -->
    <label>
        <input type="radio" name="order" <?php
        if ($order == "ASC") {
            echo "checked";
        } ?> value="ASC">
    </label>ASC
    <label>
        <input type="radio" name="order" <?php
        if ($order == "DESC") {
            echo "checked";
        } ?> value="DESC">
    </label>DESC
    <br/>

    <!-- search text-field to narrow results by user-id, email, name, etc. -->
    <label for='search'>Search:</label>
    <input type='text' name='search' value='<?php
    echo $search; ?>' id='search'/>
    <input type='submit'>

    <!-- this php block builds 'Previous' and 'Next' buttons for paginated results -->
    <?php
    /* get base-url path */
    $base_url = parse_url($_SERVER['REQUEST_URI'])['path'];

    /* if not viewing the first page of results */
    if ($page > 1) {
        $query = [
            'radio' => $radio,
            'page' => $page - 1,
            'search' => $search,
            'sorting' => $sorting,
            'order' => $order
        ];
        /* build a new url retaining query (view, search-value and decremented page-number) */
        $url = $base_url . "?" . http_build_query($query);
        /* link to previous page of results */
        echo "<a href='$url'>Previous</a>\t";
    }

    /* if there are remaining pages to view */
    if ($page < $page_count) {
        $query = [
            'radio' => $radio,
            'page' => $page + 1,
            'search' => $search,
            'sorting' => $sorting,
            'order' => $order
        ];
        /* build a new url retaining query (view, search-value and incremented page-number) */
        $url = $base_url . "?" . http_build_query($query);
        /* link to next page of results */
        echo "<a href='$url'>Next</a>";
    }
    ?>

    <!-- begin building table which displays users -->
    <table class='data-table'>
        <thead>
        <!-- build table headers for either users or transactions -->
        <tr class='ui-secondary-color'>
            <?php
            if ($radio == 'users') {
                foreach ($user_attributes as $a) { ?>
                    <th class='ui-secondary-color'> <?php echo $a ?> </th>
                <?php
                }
            } else {
                foreach ($transaction_attributes as $a) { ?>
                    <th class='ui-secondary-color'> <?php echo $a ?> </th>
                <?php
                }
            } ?>
        </tr>
        </thead>
        <tbody>

        <?php
        /* given results is not empty */
        if (!empty($results)) {
            /* for each item */
            foreach ($results as $row) {
                /* if users radio-button selected */
                if ($radio == 'users') { ?>
                    <!-- build a new table-row displaying user-id, email,
                         first name, and last name -->
                    <tr class='data-row'>
                        <!-- make user_id attribute a link which redirects to user-information page -->
                        <td>
                            <a href="userinfo.php?id=<?php echo $row['user_id'] ?>"><?php echo $row['user_id'] ?></a>
                        </td>
                        <td><?php echo $row['email'] ?></td>
                        <td><?php echo $row['first'] ?></td>
                        <td><?php echo $row['last'] ?></td>
                    </tr>
                    <!-- if transactions radio-button selected -->
                    <?php
                } else { ?>
                    <!-- build a new table-row displaying transaction-id, user-id,
                         timestamp (formatted), amount, status, and payment method -->
                    <tr class='data-row'>
                        <!-- make user_id attribute a link which redirects to user-information page -->
                        <td><?php echo $row['transaction_id'] ?></td>
                        <td>
                            <a href="userinfo.php?id=<?php echo $row['user_id'] ?>"><?php echo $row['user_id'] ?></a>
                        </td>
                        <td><?php echo date("m/d/Y", strtotime($row['timestamp'])) ?></td>
                        <td><?php echo $row['amount'] ?></td>
                        <td><?php echo $row['status'] ?></td>
                        <td><?php echo $row['payment_method'] ?></td>
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
