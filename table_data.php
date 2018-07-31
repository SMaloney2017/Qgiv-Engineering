<?php
include "curl_call.php";

// next step would be to get some results using a foreach
// loop. You have to be specific with the data that you're looking for
// array slice limits the loops that are made

foreach(array_slice($data['results'], 0, 5) as $data) {

	// by using ucwords it makes the names in caps
  	$firstName = ucwords($data['name']['first']);
    $lastName  = ucwords($data['name']['last']);
    $address   = ucwords($data['location']['street']);
    $city      = ucwords($data['location']['city']);
    $state     = ucwords($data['location']['state']);
    $zipCode   = $data['location']['postcode'];
    $email     = $data['email'];
    $dob       = $data['dob']['date'];
   // function that will push the data into the db
   // pass in the parameters in order to be used with the query
   $newPerson = new User();

   $newPerson->insert_UserData($firstName, $lastName, $dob, $address, $city, $state, $zipCode);

   // method calls in order to get the appropriate data
   // the User class is down below 
   $transStatus   = $newPerson->userTransactionStatuses();
   $transAmount   = $newPerson->randomTransAmount();
   $transactionID = $newPerson->transID();
   $userId        = $newPerson->grabUserId();
   $transDate     = $newPerson->grabCurrentDate();
   $randPayMethod = $newPerson->userPaymentMethod();

   // displays the data on the front end
    echo "<tr class='data-row'>";
    	echo "<td>" . $transactionID . "</td>";
    	echo "<td>" . $transDate . "</td>";
    	echo "<td><a href='table_data.php?$newPerson->viewUserDetails($userId);'>" . $userId . "</a></td>";
    	echo "<td>" . $firstName . "</td>";
       	echo "<td>" . $lastName . "</td>";
       	echo "<td>" . $address . "</td>";
       	echo "<td>" . $city . "</td>";
       	echo "<td>" . $state . "</td>";
       	echo "<td>" . $zipCode . "</td>";
       	echo "<td>" . $transStatus . "</td>";
    	echo "<td>" . $transAmount . "</td>";
    	echo "<td>" . $randPayMethod . "</td>";
    echo "</tr>";

 }; // end of foreach loop


// next step would be to use a class to add additional data
// timestamp, transaction status, amount, payment method
// using a class in order to organize the data
class User {

   // this method inserts the data in the db
	public function insert_UserData($firstName, $lastName, $dob, $address, $city, $state, $zipCode) {

		// making the connection global within this function is referencing
		// all instances of this variable even the global ones that are
		// outside of this method
		global $connection;

		// grabs random number between 1000 and 2000
		// next step is to make sure it isn't doubled
		$transaction_id     = $this->transID();

		// random transaction amount rounded
		$transaction_amount = $this->randomTransAmount();

		// grabs the returned variable from the userPaymentMethod method
		$userPaymentChoice  = $this->userPaymentMethod();

		// grabs the returned variable from the userTransactionStatuses method
		$userTransStatus    = $this->userTransactionStatuses();

		// current date in formatted manner
		$currentDate        = $this->grabCurrentDate();

		// grab the user's dob
		$insertDob          = $this->formatDateOfBirth($dob);

		// main query that inserts the data to the db
		$query = "INSERT INTO `transactions` (transaction_number, transaction_date, firstName, lastName, dob, address, cityName, state, postalCode, amount, transactionStatus, paymentMethod) VALUES ('$transaction_id', '$currentDate', '$firstName', '$lastName', '$insertDob', '$address', '$city', '$state', '$zipCode', '$transaction_amount', '$userTransStatus', '$userPaymentChoice')";

		// mysqli_query is a prebuilt function that runs the query
		// agains a database
		$result = mysqli_query($connection, $query);
		if(!$result) {
			// by having mysqli_error it will be a bit more
			// detailed if the query fails
			die("Query failed! ". mysqli_error($connection));
		}
	} // end of insert_UserData method


	// create an array of info that includes Payment Method
	// ex. $paymentMethod = ["Visa", "MasterCard", "eCheck", "Discover", "PayPal"]
	// from there create a function that grabs one of those values at 
	// random and assigns them once the insertData query is run
	public function userPaymentMethod() {

	 	$paymentMethods = ["Visa","American Express", "MasterCard", "eCheck", "Discover", "PayPal"];

	 	$randomMethod   = array_rand($paymentMethods);

	 	$randomPaymentMethod = $paymentMethods[$randomMethod];

	 	return $randomPaymentMethod;

	 } // end of random payment method function


	public function randomTransAmount() {
		// random transaction amount rounded
		$transaction_amount = "$" . round(mt_rand(10, 1000)/22);

		return $transaction_amount;
	} // end of randomTransAmount function


	// assigns a specific number to the transaction
	// this is at random 
	public function transID() {
		// grabs random number between 1000 and 2000
		// next step is to make sure it isn't doubled
		// reason why i used mt rand is because it's 4x faster than rand()
		$transaction_id = mt_rand(1000, 2000);

		return $transaction_id;
	} // end of transID function


	// method returns a result of a random value of the $transactionStatus
	// array
	public function userTransactionStatuses() {

		$transactionStatus = ["Accepted", "Declined", "Damn, you Broke"];

		$randomTransactionStatus = array_rand($transactionStatus);

		$randomTransStatus = $transactionStatus[$randomTransactionStatus];

		return $randomTransStatus;

	} // end of random user transaction statutes

	// 
	public function grabCurrentDate() {
		// current date in formatted manner
		// had to make sure the timezone was set correctly
		date_default_timezone_set("America/New_York");
		$currentDate = date('m/d/y');

		return $currentDate;
	} // end of grabCurrentDate function


	// grabs specific user ids from the db
	public function grabUserId() {
		global $connection;

		$userQuery = "SELECT * FROM transactions";

		$userIDs = mysqli_query($connection, $userQuery);

		if(!$userIDs) {
			die("This Query is dead!");
		} // end of if statement
		// mysqli_fetch_assoc creates the results from the mysql db
		// into an associative array 
		while($row = mysqli_fetch_assoc($userIDs)) {
			$id = $row['user_id'];
		}

		return $id;
	}


	 /* create the dob format method here
	 use the $dob variable and see how you can do so using a php
	 prebuilt function
	 you want it to look like this January 1, 1990
	 the code below works
	 */
	public function formatDateOfBirth($dob) {

    // DateTime is used to remove the time from the returned 
		$createDate = new DateTime($dob);

    	$strippedDate = $createDate->format('F d, Y');

	    return $strippedDate;

	 } // end of formatDateOfBirth function

	// clicking the User ID takes you to a view that displays that users
	// details (this will be a bit difficult)

	public function viewUserDetails($userId) {

		global $connection;

		$specificUser = "SELECT * FROM `transactions` WHERE user_id = '$userId'";

		$result = mysqli_query($connection, $specificUser);

		while($row= mysqli_fetch_assoc($result)) {
			print_r($row);
		}
	}
 
} // end of User Class

?>





       
       