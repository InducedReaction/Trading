<?php
require_once 'memoryinclude.php';
require_once 'connect.php';
require_once 'library.php';

// Set consumer key and secret
$consumerKey = readaddress("etrade_consumer_key");
$consumerSecret = readaddress("etrade_consumer_secret");


// OAuth 1 authentication
$oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
$oauth->disableSSLChecks();

// Get the request token and secret
$requestTokenInfo = $oauth->getRequestToken("https://api.etrade.com/oauth/request_token", "oob");
$requestToken = $requestTokenInfo['oauth_token'];
$requestTokenSecret = $requestTokenInfo['oauth_token_secret'];

// Save request token and secret to be used later
writetoaddress("etrade_request_token", $requestToken);
writetoaddress("etrade_request_token_secret", $requestTokenSecret);

// Redirect to E*TRADE authorization page
$authorizationUrl = "https://us.etrade.com/e/t/etws/authorize?key=".$consumerKey."&token=".$requestToken;
?>

<!DOCTYPE html>
<html>
<head>
    <title>E*TRADE API Login</title>
    <script>
        function openAuthUrl() {
            const authUrl = '<?php echo $authorizationUrl; ?>';
            window.open(authUrl, '_blank');
        }
    </script>
</head>
<body>
    <h1>E*TRADE API Login</h1>
    <p>
        <button onclick="openAuthUrl()">Open E*TRADE Authorization URL</button>
    </p>
    <form action="etradetoken.php" method="post">
        <label for="code">Enter the code from E*TRADE:</label>
        <input type="text" id="code" name="code" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>

<?php
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
        writetoaddress("etrade_access_token", $accessToken);
        writetoaddress("etrade_access_token_secret", $accessTokenSecret);

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
