<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past


ini_set('max_execution_time', '0'); 
// Turn off all error reporting
//error_reporting(0);

include_once'library.php';

$link = mysqli_connect("localhost", "root", "password") or die(mysqli_error());

date_default_timezone_set('America/Los_Angeles');

// Make a MySQL Connection
mysqli_query($link, "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8'");
mysqli_select_db($link, "gobrrr") or die(mysql_error());
$time=time();
$count=0;
$error=0;


$query=mysqli_query($link, "SELECT * FROM `gobrrr`.`users` WHERE `id`='1'");
$row=mysqli_fetch_array($query);

$code1 = $row['access_token'];
$refresh = $row['refresh_token'];



if(!isset($_COOKIE["gobrrr"])){
	//header('Location: index.php');
	$value = generateRandomString(255);
	setcookie("gobrrr", $value, time()+2*365*24*60*60); 
	$cookie=$value;
}else{
	$cookie=$_COOKIE["gobrrr"];
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

include_once'memoryinclude.php';


$clientid="";//Schwab Client id
$secret="";//Schwab secret

/**********************************************************************************/
//
function getUnderlyingSymbolAndReverseStrategy($ticker, $quantity) {
    if(str_contains($ticker, "_")) {
        $pieces = explode("_", $ticker);
        $underlyingSymbol = $pieces[0];
        if(str_contains($pieces[1], "C")) {
            $callputcommon = "call";
            $reversestrategy = $quantity > 0 ? 6 : 8;
        } else {
            $callputcommon = "put";
            $reversestrategy = $quantity > 0 ? 5 : 7;
        }
    } else {
        $underlyingSymbol = $ticker;
        $callputcommon = "common";
        $reversestrategy = null;
    }
    return [$underlyingSymbol, $callputcommon, $reversestrategy];
}

function technicalExitCheck($ticker, $quantity, $callputcommon, $clayrow) {
    $nkt = 0;
    if($clayrow['technicalexit']=="1"&&$clayrow['killtrade']!=1){
        $tech = entrytrigger($ticker);
        if($tech[0]==true){
            if(str_contains($tech[2], "Bullish")){
                $nkt = (($quantity>0&&$callputcommon=="put")||($quantity<0&&$callputcommon=="call")||($quantity<0&&$callputcommon=="common")) ? 1 : 0;
            }else{
                $nkt = (($quantity<0&&$callputcommon=="put")||($quantity>0&&$callputcommon=="call")||($quantity>0&&$callputcommon=="common")) ? 1 : 0;
            }
        }
    }
    return $nkt;
}

?>