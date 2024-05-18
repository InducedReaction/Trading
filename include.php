<?php

$optionchainCache = array();
function getoptionchain($ticker){
	global $optionchainCache, $code1;
	$cacheKey = $ticker;
	if(isset($optionchainCache[$cacheKey])) {
		//echo "<br>reusing!";
        return $optionchainCache[$cacheKey];
    }
	$processrandomid=time().rand(0,1000)."getbestoption";
	tdsleep($processrandomid);
	// Define the base URL and parameters for the Schwab API request
	$schwab_base_url = 'https://api.schwabapi.com/marketdata/v1/chains';

	// Construct the query parameters
	$query_params = http_build_query([
		'symbol' => $ticker, // e.g., 'AAPL'
		'contractType' => 'ALL', // Fetch all types of contracts
		'includeUnderlyingQuote' => 'TRUE', // Include underlying quote information
		'strategy' => 'SINGLE', // Fetch single leg strategies
		'range' => 'ALL', // Fetch all options
		'optionType' => 'ALL', // Fetch all types of options
	]);

	// Initialize the cURL session
	$ch = curl_init("$schwab_base_url?$query_params");

	// Set options for the cURL request
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	$headers = [
		'Authorization: Bearer ' . $code1, // Replace $code1 with your actual bearer token
		'Accept: application/json' // Ensuring we specify that we want JSON responses
	];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// Execute the cURL session
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Curl error: ' . curl_error($ch);
	}
	curl_close($ch);

	// Decode the JSON response
	$data = json_decode($result, true);
	$optionchainCache[$cacheKey]=$data;
	return $data;
}



?>
