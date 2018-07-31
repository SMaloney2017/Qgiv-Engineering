<?php include "curl_call.php" ?>


<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width" />
    
    <title>Qgiv Engineering Exercise</title>
    
    <link rel="stylesheet" type="text/css" href="https://secure.qgiv.com/resources/admin/css/application.css" />

    <!-- jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <!-- external js file -->
    <!-- <script src="ajaxCall.js"></script> -->

    <style type="text/css">
        .container{ max-width: 1200px; margin: 0 auto; }
        .logo-header{ padding: 2em; }
        .logo{ margin: 0 auto; min-height: 80px; }
    </style>


</head>

<body>
    <section class="container">
        <div class="logo-header">
            <img class="logo" src="https://secure.qgiv.com/resources/core/images/logo-qgiv.svg" alt="Qgiv logo" />
        </div>
        <!-- table that contains the data -->
        <br />
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="ui-secondary-color">Transaction #</th>
                        <th class="ui-secondary-color">Transaction Date</th>
                        <th class="ui-secondary-color">User ID</th>
                        <th class="ui-secondary-color">First Name</th>
                        <th class="ui-secondary-color">Last Name</th>
                        <th class="ui-secondary-color">Address</th>
                        <th class="ui-secondary-color">City</th>
                        <th class="ui-secondary-color">State</th>
                        <th class="ui-secondary-color">Zip</th>
                        <th class="ui-secondary-color">Transaction Status</th>
                        <th class="ui-secondary-color">Amount</th>
                        <th class="ui-secondary-color">Payment Method</th>
                    </tr>
                </thead>
                <tbody>     
                        <?php include "table_data.php"; ?>
                </tbody>
            </table>
       </div>
    </section>
</body>
</html>