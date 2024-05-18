 <?php
ini_set('max_execution_time', '0'); 
include_once'connect.php';
$error=0;
//echo "<br>".$code1."<br>";
$processrandomid="Refresher";
//tdsleep($processrandomid);
$query=mysqli_query($link, "SELECT * FROM `gobrrr`.`users` WHERE `id`='1'");
$row=mysqli_fetch_array($query);

$code1 = $row['access_token'];
$refresh = $row['refresh_token'];
$base64Credentials = base64_encode($clientid . ':' . $secret);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.schwabapi.com/v1/oauth/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$postFields = "grant_type=refresh_token&refresh_token=" . urlencode($refresh);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

$headers = array(
    'Authorization: Basic ' . $base64Credentials,
    'Content-Type: application/x-www-form-urlencoded'
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    $error = curl_error($ch);
    curl_close($ch);
    die('Curl error: ' . $error);
}
curl_close($ch);

$data = json_decode($result, true);


//echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
if (isset($data['access_token'])) {
    $newAccessToken = $data['access_token'];
    $newRefreshToken = isset($data['refresh_token']) ? $data['refresh_token'] : $refreshToken;  // Fallback to old refresh token if not provided
    $expiresIn = $data['expires_in'];
    $time = time();

    mysqli_query($link, "UPDATE `users` SET `access_token` = '$newAccessToken', `refresh_token` = '$newRefreshToken', `expires_in` = '$expiresIn', `time` = '$time' WHERE `users`.`id` = 1;");

    // Assuming writetoaddress is a function to write data to some storage
    writetoaddress("access_token", $newAccessToken);
    writetoaddress("access_token_last_refreshed", microtime(true));
} else {
    echo "Error refreshing token: " . (isset($data['error_description']) ? $data['error_description'] : 'Unknown error');
}
etrade_renewtoken();
//header('Location: hammer.php?code='.$code);


//echo $result;
?>
