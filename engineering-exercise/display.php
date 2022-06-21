<?php

require './database/Connection.php';
require './user/User.php';

/* max rows for pagination */
const MAX_ROWS = 15;

/* attributes for dynamically building menu's and headers,
   as well as verifying that the selected ordering is valid */
const USER_ATTRIBUTES = [
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

/**
 * Helper function for building radio buttons,
 * where name is the name of the variable,
 * value is the reference to the variable,
 * and label is the actual value.
 *
 * @param string $name
 * @param string $value
 * @param string $label
 * @return void
 */
function buildRadioButton(string $name, string $value, string $label): void
{
    echo "<label for=$name>" . ucfirst($label) . "</label>\n";
    echo "<input type='radio' name=$name value=$label" . ($label == $value ? ' checked' : '') . ">\n";
}

/**
 * Helper function for building navigation buttons for paginated
 * results. Variables passed in are used to build an url which
 * retains the query as the page reloads.
 *
 * @param int $page
 * @param int $page_count
 * @param string $radio
 * @param string $search
 * @param string $sorting
 * @param string $order
 * @return void
 */
function buildNavigation(
    int $page,
    int $page_count,
    string $radio,
    string $search,
    string $sorting,
    string $order
): void {
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
}

/**
 * Helper function which builds the menu entries used to select
 * which attribute to use as the query's sorting value.
 *
 * @param string $sorting
 * @param array $attributes
 * @return void
 */
function buildMenuEntries(string $sorting, array $attributes): void
{
    echo "<label for='sorting'>Sort by:\n</label>";
    echo "<select id='sorting' name='sorting'>";
    foreach ($attributes as $a) {
        echo "<option value=$a" . ($sorting == $a ? " selected" : "") . ">$a</option>";
    }
    echo "</select>\n";
}

/**
 * Helper function which builds a text-field used to narrow results.
 * Searching users finds similar values to user_id, email, first-name, and last-name.
 * Searching transactions finds similar values to transaction_id and user_id.
 *
 * @param string $value
 * @return void
 */
function buildSearchField(string $value): void
{
    echo "<label for='search'>Search:\n</label>";
    echo "<input type='text' id='search' name='search' value=$value>\n";
    echo "<input type='submit'>\n";
}

/**
 * Helper function which builds the table-header.
 *
 * @param array $attributes
 * @return void
 */
function buildTableHeader(array $attributes): void
{
    echo "<thead><tr class='ui-secondary-color'>";
    foreach ($attributes as $a) {
        echo "<th class='ui-secondary-color'>$a</th>";
    }
    echo "</tr></thead>";
}

/**
 * Helper function which builds the table for the queried results.
 *
 * @param array $results
 * @param array $attributes
 * @return void
 */
function buildTableEntries(array $results, array $attributes): void
{
    echo "<tbody>";
    if (!empty($results)) {
        /* for each item */
        foreach ($results as $row) {
            echo "<tr class='data-row'>";
            foreach ($attributes as $a) {
                if ($a == 'user_id') {
                    echo "<td><a href='userinfo.php?id=$row[$a]'>$row[$a]</a></td>";
                } else {
                    echo "<td>$row[$a]</td>";
                }
            }
            echo "</tr>";
        }
    }
    echo "</tbody>";
}

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
if (!in_array($sorting, $radio == 'users' ? USER_ATTRIBUTES : TRANSACTION_ATTRIBUTES)) {
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
<?php require 'htmlhead.php' ?>

<body>
<form name='search' method='get'>

    <!-- this php block builds the tools for selecting the current view and
         querying the database, including search, sorting, and pagination -->
    <?php
    /* build radio buttons to select view (transactions / users) */
    buildRadioButton("radio", $radio, "transactions");
    buildRadioButton("radio", $radio, "users");
    echo "<br><hr>";

    /* if users radio button selected, build user menu-entries
       otherwise, build transaction menu-entries */
    if ($radio == 'users') {
        buildMenuEntries($sorting, USER_ATTRIBUTES);
    } else {
        buildMenuEntries($sorting, TRANSACTION_ATTRIBUTES);
    }

    /* build radio buttons for sorting (ascending / descending) */
    buildRadioButton("order", $order, "ASC");
    buildRadioButton("order", $order, "DESC");
    echo "<br><hr>";

    /* search text-field to narrow results by user-id, email, name, etc. */
    buildSearchField($search);

    /* builds 'Previous' and 'Next' buttons for paginated results */
    buildNavigation($page, $page_count, $radio, $search, $sorting, $order);
    echo "<br><hr>";
    ?>

    <!-- begin building table which displays users -->
    <table class='data-table'>
        <?php
        if ($radio == 'users') {
            buildTableHeader(USER_ATTRIBUTES);
            buildTableEntries($results, USER_ATTRIBUTES);
        } else {
            buildTableHeader(TRANSACTION_ATTRIBUTES);
            buildTableEntries($results, TRANSACTION_ATTRIBUTES);
        }
        ?>
    </table>
</form>
</body>
</html>
