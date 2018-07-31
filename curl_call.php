<?php
// use php to grab contents from url https://randomuser.me/api/?results=500&nat=us&exc=login,id,nat
$url = 'https://randomuser.me/api/?results=500&nat=us&exc=login,id,nat';

$curl = curl_init();

// disables SSL verification
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// returns the response, if false it prints the response
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// set the url
curl_setopt($curl, CURLOPT_URL, $url);

// send the request and save it to $resp
$resp = curl_exec($curl);

// close the request
curl_close($curl);

// the json decode transforms the data into an associative array
$data = json_decode($resp, true);


// mysql connection
$connection = mysqli_connect('localhost', 'root', '', 'user_transactions');
	if(!$connection) {
		die("Database isn't working");
	} 
?>