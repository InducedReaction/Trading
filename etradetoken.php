<?php
require_once 'memoryinclude.php';
require_once 'connect.php';
require_once 'library.php';

echo readaddress("etrade_request_token");
// Set consumer key and secret
$consumerKey = readaddress("etrade_consumer_key");
$consumerSecret = readaddress("etrade_consumer_secret");

// OAuth 1 authentication
$oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
$oauth->disableSSLChecks();

// Use the authorization code to get the access token and secret
if (isset($_POST['code'])) {
    $authCode = $_POST['code'];
    try{
        // Get the request token and secret
        $requestToken = readaddress("etrade_request_token");
        $requestTokenSecret = readaddress("etrade_request_token_secret");

        $oauth->setToken($requestToken, $requestTokenSecret);
        $accessTokenInfo = $oauth->getAccessToken("https://api.etrade.com/oauth/access_token", null, $authCode);
        $accessToken = $accessTokenInfo['oauth_token'];
        $accessTokenSecret = $accessTokenInfo['oauth_token_secret'];

        // Save access token and secret to be used later
        writetoaddress("etrade_oauth_token", $accessToken);
        writetoaddress("etrade_oauth_token_secret", $accessTokenSecret);

        // Use the access token and secret to create an authenticated OAuth 1 session
        $oauth->setToken($accessToken, $accessTokenSecret);
        $oauth->fetch("https://api.etrade.com/v1/accounts/list");
        $response = $oauth->getLastResponse();
		//echo "<br>";
        //echo $response;
		
		
		$accessToken=readaddress("etrade_oauth_token");
		$accessTokenSecret=readaddress("etrade_oauth_token_secret");
		
				// OAuth 1 authentication
		$oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
		$oauth->disableSSLChecks();
        // Use the access token and secret to create an authenticated OAuth 1 session
        $oauth->setToken($accessToken, $accessTokenSecret);
        $oauth->fetch("https://api.etrade.com/v1/accounts/list.json");
        $response = $oauth->getLastResponse();
		//echo "<br>";
        //echo $response;
		$data = json_decode($response, true);
		
		//echo "<br>";
		
		// Print the entire array
		//echo "<pre>";  // This will format the output for readability
		//print_r($data);
		//echo "</pre>";

		if (isset($data['AccountListResponse']['Accounts']['Account']) && is_array($data['AccountListResponse']['Accounts']['Account'])) {
			foreach ($data['AccountListResponse']['Accounts']['Account'] as $account) {
				if (isset($account['accountId'])) {
					//echo "Account ID: " . $account['accountId'] . "<br>";
					$accountid = $account['accountId'];
					
					$accountidkey = $account['accountIdKey'];
					$insttype = $account['institutionType'];
					writetoaddress("accountId",$account['accountId']);
					writetoaddress("accountIdKey",$account['accountIdKey']);
					writetoaddress("institutionType",$account['institutionType']);
				}
			}
		} else {
			//echo "Account structure not as expected.";
		}
		

    } catch (Exception $e) {
        //echo "Error: " . $e->getMessage();
    }
}
header("Location: index.php");
?>