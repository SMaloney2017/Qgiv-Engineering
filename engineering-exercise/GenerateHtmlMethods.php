<?php

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
                if ($a == 'timestamp') {
                    $formatted_date = date('m/d/Y', strtotime($row[$a]));
                    echo "<td>$formatted_date</td>";
                } else {
                    if ($a == 'user_id') {
                        echo "<td><a href='Information.php?id=$row[$a]'>$row[$a]</a></td>";
                    } else {
                        echo "<td>$row[$a]</td>";
                    }
                }
            }
            echo "</tr>";
        }
    }
    echo "</tbody>";
}

/**
 * Helper function which builds the complete table.
 *
 * @param array $results
 * @param array $attributes
 * @return void
 */
function buildTable(array $results, array $attributes): void
{
    echo "<table class='data-table'>";
    buildTableHeader($attributes);
    buildTableEntries($results, $attributes);
    echo "</table>";
}