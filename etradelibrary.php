<?php
function etrade_renewtoken(){
	try {	
		$consumerKey = readaddress("etrade_consumer_key");
		$consumerSecret = readaddress("etrade_consumer_secret");

		$accessToken=readaddress("etrade_oauth_token");
		$accessTokenSecret=readaddress("etrade_oauth_token_secret");
		$accountid = readaddress("accountId");
		$accountidkey = readaddress("accountIdKey");
		$insttype = readaddress("institutionType");
		//renew access token.
	// OAuth 1 authentication
			$oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
			$oauth->disableSSLChecks();
			// Use the access token and secret to create an authenticated OAuth 1 session
			$oauth->setToken($accessToken, $accessTokenSecret);
			
			$oauth->fetch("https://api.etrade.com/oauth/renew_access_token");
			return $response = $oauth->getLastResponse();
	}catch (OAuthException $e) {
        // Handle the exception or log error details
        // e.g., error_log($e->getMessage());
        return false;
    }        
}

function etrade_revoketoken(){
	$consumerKey = readaddress("etrade_consumer_key");
	$consumerSecret = readaddress("etrade_consumer_secret");

	$accessToken=readaddress("etrade_oauth_token");
	$accessTokenSecret=readaddress("etrade_oauth_token_secret");
	$accountid = readaddress("accountId");
	$accountidkey = readaddress("accountIdKey");
	$insttype = readaddress("institutionType");
	//renew access token.
// OAuth 1 authentication
		$oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
		$oauth->disableSSLChecks();
        // Use the access token and secret to create an authenticated OAuth 1 session
        $oauth->setToken($accessToken, $accessTokenSecret);
        
        $oauth->fetch("https://api.etrade.com/oauth/revoke_access_token");
        return $response = $oauth->getLastResponse();
		
        
}

function etrade_balance() {
    try {
        $consumerKey = readaddress("etrade_consumer_key");
        $consumerSecret = readaddress("etrade_consumer_secret");

        $accessToken = readaddress("etrade_oauth_token");
        $accessTokenSecret = readaddress("etrade_oauth_token_secret");
        $accountidkey = readaddress("accountIdKey");
        $insttype = readaddress("institutionType");

        $oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
        $oauth->disableSSLChecks();
        $oauth->setToken($accessToken, $accessTokenSecret);
        
        $oauth->fetch("https://api.etrade.com/v1/accounts/".$accountidkey."/balance.json?instType=".$insttype."&realTimeNAV=true");
        $response = $oauth->getLastResponse();
        return json_decode($response, true);
    } catch (OAuthException $e) {
        // Handle the exception or log error details
        // e.g., error_log($e->getMessage());
        return false;
    }
}


function etrade_portfolio(){
    $consumerKey = readaddress("etrade_consumer_key");
    $consumerSecret = readaddress("etrade_consumer_secret");

    $accessToken = readaddress("etrade_oauth_token");
    $accessTokenSecret = readaddress("etrade_oauth_token_secret");
    $accountid = readaddress("accountId");
    $accountidkey = readaddress("accountIdKey");
    $insttype = readaddress("institutionType");

    // OAuth 1 authentication
    $oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
    $oauth->disableSSLChecks();
    $oauth->setToken($accessToken, $accessTokenSecret);

    try {
        $oauth->fetch("https://api.etrade.com/v1/accounts/".$accountidkey."/portfolio.json?instType=".$insttype."&realTimeNAV=true");
        $response = $oauth->getLastResponse();
        return json_decode($response, true);
    } catch (OAuthException $e) {
        // Handle exception here
        // You can log the error or return a custom error message
        error_log("OAuthException: " . $e->getMessage());
        return null; // or return a custom error array/message
    }
}



function etrade_preview_order($orderDetails) {
    $consumerKey = readaddress("etrade_consumer_key");
    $consumerSecret = readaddress("etrade_consumer_secret");
    $accessToken = readaddress("etrade_oauth_token");
    $accessTokenSecret = readaddress("etrade_oauth_token_secret");
    $accountidkey = readaddress("accountIdKey");

    $oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
    $oauth->disableSSLChecks();
    $oauth->setToken($accessToken, $accessTokenSecret);

    // Construct the URL for previewing the order
    $url = "https://api.etrade.com/v1/accounts/" . $accountidkey . "/orders/preview.json";

    try {
        // Set the headers for the POST request
        $headers = array('Content-Type' => 'application/xml');

        // Use the OAuth session to send a POST request
        $oauth->fetch($url, $orderDetails, OAUTH_HTTP_METHOD_POST, $headers);

        $response = $oauth->getLastResponse();

        // Check the format of the response and parse accordingly
        // Assuming the response is JSON
        return json_decode($response, true);
    } catch (OAuthException $e) {
        // Handle any exceptions/errors here
        // For example, you could log the error and return a custom error message
        error_log($e->getMessage());  // Log the error for debugging
        return array('error' => 'An error occurred while processing your request.');
    }
}



function etrade_place_order($orderDetails) {
	
    $consumerKey = readaddress("etrade_consumer_key");
    $consumerSecret = readaddress("etrade_consumer_secret");
    $accessToken = readaddress("etrade_oauth_token");
    $accessTokenSecret = readaddress("etrade_oauth_token_secret");
    $accountidkey = readaddress("accountIdKey");

    $oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
    $oauth->disableSSLChecks();
    $oauth->setToken($accessToken, $accessTokenSecret);


    // Construct the URL for placing the order
    $url = "https://api.etrade.com/v1/accounts/" . $accountidkey . "/orders/place.json";

    try {
        // Set the headers for the POST request
        $headers = array('Content-Type' => 'application/xml');

        // Use the OAuth session to send a POST request
        $oauth->fetch($url, $orderDetails, OAUTH_HTTP_METHOD_POST, $headers);

        $response = $oauth->getLastResponse();

        // Check the format of the response and parse accordingly
        // Assuming the response is JSON
        return json_decode($response, true);
    } catch (OAuthException $e) {
        // Handle any exceptions/errors here
        // For example, you could log the error and return a custom error message
        error_log($e->getMessage());  // Log the error for debugging
        return array('error' => 'An error occurred while processing your request.'.$e);
    }
}


function etrade_get_quote($ticker) {
    $consumerKey = readaddress("etrade_consumer_key");
    $consumerSecret = readaddress("etrade_consumer_secret");
    $accessToken = readaddress("etrade_oauth_token");
    $accessTokenSecret = readaddress("etrade_oauth_token_secret");
    $accountidkey = readaddress("accountIdKey");

    $oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
    $oauth->disableSSLChecks();
    $oauth->setToken($accessToken, $accessTokenSecret);
    $consumerKey = readaddress("etrade_consumer_key");
    
    $url = "https://api.etrade.com/v1/market/quote/".$ticker.".json?detailFlag=ALL";
    
    try {
        $oauth->fetch($url, null, OAUTH_HTTP_METHOD_GET, array('Content-Type' => 'application/json'));
        $response = $oauth->getLastResponse();
        $data = json_decode($response, true);
        return $data; // Or process $data as needed
    } catch (OAuthException $e) {
        error_log($e->getMessage());
        return null; // Or handle the error as preferred
    }
}


?>