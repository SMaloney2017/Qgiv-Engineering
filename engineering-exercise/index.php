<?php
/*
- ✓ create a Database to hold transaction records and users
- ✓ loads in a list of users from JSON: https://randomuser.me/api/?results=500&nat=us&exc=login,id,nat
    - ✓ add unique users to a Database view
    - ✓ as users are loaded, generate a transaction record tied to the User
    - ✓ create a User class to work with the data
        - ✓ use the class to format birthdate to the following format: January 1, 1990
        - ✓ write a method to generate a transaction for the User
            - in addition to storing User details, transactions should have:
                - ✓ a unique ID
                - ✓ timestamp
                - ✓ amount
                - ✓ status
                - ✓ payment method (Visa, Mastercard, Discover, American Express, eCheck, or any other new payment method that pops up)
        - anything else you think would be helpful for working with the data

- ✓ view a view of transactions with data displayed
- ✓ format transactions in the following format: MM/DD/YYYY
- ✓ default sorted by transaction ID

- ✓ view a view of users with data displayed
- ✓ clicking the User ID takes you to a view that displays that users details and associated transactions

- note: logic should be built without utilizing PHP libraries

- ✓ bonus: add dynamic view sorting
- ✓ bonus: add pagination
*/

?>
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

    <!-- list of users/transactions, including search, pagination, and dynamic sorting  -->
    <?php
    require 'Display.php' ?>

</section>
</body>
</html>
