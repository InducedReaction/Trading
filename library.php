<?php


include_once'etradelibrary.php';


function convertOptionTicker($ticker) {
    // Remove any spaces from the ticker
    $ticker = str_replace(' ', '', $ticker);

    // Assuming the symbol can vary in length, find the first digit as the start of the date
    $firstDigitPos = strcspn($ticker, '0123456789');

    // Extract the symbol
    $symbol = substr($ticker, 0, $firstDigitPos);

    // The next 6 characters are the date in YYMMDD format
    $date = substr($ticker, $firstDigitPos, 6);
    // Reformat the date to MMDDYY
    $day = substr($date, 4, 2);
    $month = substr($date, 2, 2);
    $year = substr($date, 0, 2);

    // Option type (C or P)
    $optionType = substr($ticker, $firstDigitPos + 6, 1);

    // Strike price (remaining characters), divided by 1000 and convert to integer
    $strikePrice = intval(substr($ticker, $firstDigitPos + 7)) / 1000;

    // Construct the new format
    $newFormat = "{$symbol}_{$month}{$day}{$year}{$optionType}{$strikePrice}";

    return $newFormat;
}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function connectdb(){
	$link = mysqli_connect("localhost", "root", "password") or die(mysqli_error());

	date_default_timezone_set('America/Los_Angeles');

	// Make a MySQL Connection
	mysqli_query($link, "SET character_set_results = 'utf8'");
	mysqli_query($link, "character_set_client = 'utf8'");
	mysqli_query($link, "character_set_connection = 'utf8'");
	mysqli_query($link, "character_set_database = 'utf8'");
	//mysqli_select_db($link, "ibuyoufly") or die(mysql_error());
	return $link;
}


//
function newyorktime() {
    $dateTime = new DateTime();
    $dateTime->setTimezone(new DateTimeZone('America/New_York'));
    return $dateTime;
}

function californiatime() {
    $dateTime = new DateTime();
    $dateTime->setTimezone(new DateTimeZone('America/Los_Angeles'));
    return $dateTime;
}
function getangle(){
	
}

function br(){
	return "<br>";
}

function table($e){
	echo "<table>".$e."</table>";
}
function tr($e){
	return "<tr>".$e."</tr>";
}
function td($e){
	return "<td>".$e."</td>";
}
//find in string
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

function ema($yma, $n, $price){//(yesterdays ema, number of days, today's price)
	

	 $ema = ($price*(2/($n+1))) + ($yma*(1-(2/($n+1))));
	return $ema;
}

function macdline($ema12, $ema26, $ema9, $price){
	return $macdline = ema($ema12, 12, $price) - ema($ema26, 26, $price);
	
}
function macdslowline($ema12, $ema26, $ema9, $price){
	$macdline = ema($ema12, 12, $price) - ema($ema26, 26, $price);
	
	return $slowsignalline = ema($ema9, 9, $macdline);
	
}
function macdhistogram($ema12, $ema26, $ema9, $price){
	$macdline = ema($ema12, 12, $price) - ema($ema26, 26, $price);
	$slowsignalline = ema($ema9, 9, $macdline);
	return $histogram = $macdline - $slowsignalline;
}


function calculateElderRay($highs, $lows, $closes, $emaPeriod = 26) {
    // Calculate the EMA of the closing prices for the specified period
    $ema = arr_ema($closes, $emaPeriod);

    // Initialize arrays to store Bull Power and Bear Power
    $elderRayBull = [];
    $elderRayBear = [];

    for ($i = 0; $i < count($closes); $i++) {
        // Ensure EMA is calculated for the current point
        if (isset($ema[$i])) {
            // Calculate Bull Power and Bear Power
            $elderRayBull[$i] = isset($highs[$i]) ? $highs[$i] - $ema[$i] : null;
            $elderRayBear[$i] = isset($lows[$i]) ? $lows[$i] - $ema[$i] : null;
        } else {
            $elderRayBull[$i] = null;
            $elderRayBear[$i] = null;
        }
    }

    return [
        'bullPower' => $elderRayBull,
        'bearPower' => $elderRayBear
    ];
}

function calculateElderForceIndexEMA($closes, $volumes, $period = 13) {
    $elderForceIndex = [];
	$elderForceIndex[0] = 0; // Initialize the first element to prevent undefined offset
	//$elderForceIndex[$period-1] = 0; // Initialize the first element to prevent undefined offset
    for ($i = 1; $i < count($closes); $i++) {
        // Calculate EFI for each day (except the first one)
        $elderForceIndex[$i] = ($closes[$i] - $closes[$i-1]) * $volumes[$i];
    }

    // Calculate the EMA of Elder Force Index for the specified period
    $elderForceEma = arr_ema($elderForceIndex, $period);

    return $elderForceEma; // Returns only the EMA of the Elder Force Index for the specified period
}





function arr_ema($real, $timeperiod){//ema takes in array and time period then
			$array = []; // Initialize $array as an empty array
			for($i = 0; $i < count($real); $i++){
			//Calculate the 12 EMA and set the value in an array $arr_ema12[]
			if($i==0){//calculate the 12
				$array[$i]=$real[$i];//give the EMA somewhere to start since it needs yesterdays data
			}
			else{
				$array[$i]=ema($array[$i-1], $timeperiod, $real[$i]);
			}
			/*if($i>=$timeperiod){
				$arr_ema12[$i]=ema($yesterdays12ema, 12, $real[$i]);
			}
			*/	
	}
	
	
	return $array;
}






function arr_advanced1ema($real, $timeperiod){//ema takes in array and time period then
			for($i = 0; $i < count($real); $i++){
			//Calculate the 12 EMA and set the value in an array $arr_ema12[]
			if($i==0){//calculate the 12
				$array[$i]=$real[$i];//give the EMA somewhere to start since it needs yesterdays data
			}
			else{
				$array[$i]=ema($array[$i-1], $timeperiod, $real[$i]);
			}
			/*if($i>=$timeperiod){
				$arr_ema12[$i]=ema($yesterdays12ema, 12, $real[$i]);
			}
			*/
			
	}
	
	
	return $array;
}




function calculateMACD($prices, $shortPeriod, $longPeriod, $signalPeriod) {
    $shortEma = arr_ema($prices, $shortPeriod);
    $longEma = arr_ema($prices, $longPeriod);

    $macdLine = [];
    for ($i = 0; $i < count($prices); $i++) {
        $macdLine[$i] = $shortEma[$i] - $longEma[$i];
    }

    $signalLine = arr_ema($macdLine, $signalPeriod);
    return ['macd' => $macdLine, 'signal' => $signalLine];
}


function findBestMACDCombo($prices, $maxPeriod = 48) {
    $bestProfit = PHP_INT_MIN;
    $bestShort = 0;
    $bestLong = 0;
    $bestSignal = 0;
    $n = count($prices);

    for ($shortPeriod = 1; $shortPeriod < $maxPeriod; $shortPeriod++) {
        for ($longPeriod = $shortPeriod + 1; $longPeriod < $maxPeriod + 1; $longPeriod++) {
            for ($signalPeriod = 1; $signalPeriod < $maxPeriod + 1; $signalPeriod++) {
                $macdData = calculateMACD($prices, $shortPeriod, $longPeriod, $signalPeriod);

                $currentProfit = 0;
                $position = 0;

                for ($i = 1; $i < $n; $i++) {
                    if ($macdData['macd'][$i] > $macdData['signal'][$i] && $macdData['macd'][$i-1] <= $macdData['signal'][$i-1]) {
                        $position = $prices[$i];
                    } elseif ($macdData['macd'][$i] < $macdData['signal'][$i] && $macdData['macd'][$i-1] >= $macdData['signal'][$i-1] && $position > 0) {
                        $currentProfit += $prices[$i] - $position;
                        $position = 0;
                    }
                }

                if ($currentProfit > $bestProfit) {
                    $bestProfit = $currentProfit;
                    $bestShort = $shortPeriod;
                    $bestLong = $longPeriod;
                    $bestSignal = $signalPeriod;
                }
            }
        }
    }

    return ['shortPeriod' => $bestShort, 'longPeriod' => $bestLong, 'signalPeriod' => $bestSignal, 'profit' => $bestProfit];
}

function findBestShortMACDCombo($prices, $maxPeriod = 48) {
    $bestProfit = PHP_INT_MIN;
    $bestShort = 0;
    $bestLong = 0;
    $bestSignal = 0;
    $n = count($prices);

    for ($shortPeriod = 1; $shortPeriod < $maxPeriod; $shortPeriod++) {
        for ($longPeriod = $shortPeriod + 1; $longPeriod < $maxPeriod + 1; $longPeriod++) {
            for ($signalPeriod = 1; $signalPeriod < $maxPeriod + 1; $signalPeriod++) {
                $macdData = calculateMACD($prices, $shortPeriod, $longPeriod, $signalPeriod);

                $currentProfit = 0;
                $position = 0;

                for ($i = 1; $i < $n; $i++) {
                    // Enter short position when MACD crosses below signal
                    if ($macdData['macd'][$i] < $macdData['signal'][$i] && $macdData['macd'][$i-1] >= $macdData['signal'][$i-1]) {
                        $position = $prices[$i]; // Selling
                    } 
                    // Exit short position when MACD crosses above signal
                    elseif ($macdData['macd'][$i] > $macdData['signal'][$i] && $macdData['macd'][$i-1] <= $macdData['signal'][$i-1] && $position > 0) {
                        $currentProfit += $position - $prices[$i]; // Buying back
                        $position = 0;
                    }
                }

                if ($currentProfit > $bestProfit) {
                    $bestProfit = $currentProfit;
                    $bestShort = $shortPeriod;
                    $bestLong = $longPeriod;
                    $bestSignal = $signalPeriod;
                }
            }
        }
    }

    return ['shortPeriod' => $bestShort, 'longPeriod' => $bestLong, 'signalPeriod' => $bestSignal, 'profit' => $bestProfit];
}
$dataCache = array();
function getMarketData($ticker, $access_token) {
	tdsleep("marketdata".$ticker);
	global $dataCache,$code1;
	
	$cacheKey = $ticker . "_";
	
	// Check if data is already fetched
    if (isset($dataCache[$cacheKey])) {
        return $dataCache[$cacheKey]; // Return cached data if available
    }
	if(!str_contains($ticker, "_")){
		// Define Schwab's API endpoint and set the symbol as a query parameter
		$schwab_base_url = 'https://api.schwabapi.com/marketdata/v1/quotes';
		$schwab_query_params = http_build_query([
			'symbols' => $ticker, // Assuming $ticker is already defined and contains the desired stock symbol
		]);

		// Initialize cURL session
		$ch = curl_init("$schwab_base_url?$schwab_query_params");

		// Set cURL options for the GET request
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		$headers = ['Authorization: Bearer ' . $code1]; // Update with the correct authorization token
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// Execute the cURL session
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);

		// Optionally, decode the JSON response
		$data = json_decode($result, true);
	}else{
		$parts = explode('_', $ticker); 
		$underlyingticker = $parts[0];
		
			$data=getoptionchain($underlyingticker);
			$calldates = array_keys($data["callExpDateMap"]);
			$count = count(array_keys($data["callExpDateMap"]));

			echo "underlying: <br>";
			//echo "<br>";
			$underlyinglast = $data["underlying"]["last"];
			//echo "<br>";
					for($i=0; $i<$count; $i++){
							$strikes = array_keys($data["callExpDateMap"][$calldates[$i]]);
							$count1 = count(array_keys($data["callExpDateMap"][$calldates[$i]]));
							for($i1=0; $i1<$count1; $i1++){
								$symbol = convertOptionTicker($data["callExpDateMap"][$calldates[$i]][$strikes[$i1]][0]["symbol"]);
								if($symbol==$ticker){
									$data[$ticker]['quote']["bidPrice"]=$data["callExpDateMap"][$calldates[$i]][$strikes[$i1]][0]["bid"];
									$data[$ticker]['quote']["askPrice"]=$data["callExpDateMap"][$calldates[$i]][$strikes[$i1]][0]["ask"];
									$data[$ticker]["underlyingPrice"]=$data["underlying"]["last"];
								}
							}
					}
					for($i=0; $i<$count; $i++){
							$strikes = array_keys($data["putExpDateMap"][$calldates[$i]]);
							$count1 = count(array_keys($data["putExpDateMap"][$calldates[$i]]));
							for($i1=0; $i1<$count1; $i1++){
									$symbol = convertOptionTicker($data["putExpDateMap"][$calldates[$i]][$strikes[$i1]][0]["symbol"]);
									if($symbol==$ticker){
										$data[$ticker]['quote']["bidPrice"]=$data["putExpDateMap"][$calldates[$i]][$strikes[$i1]][0]["bid"];
										$data[$ticker]['quote']["askPrice"]=$data["putExpDateMap"][$calldates[$i]][$strikes[$i1]][0]["ask"];
										$data[$ticker]["underlyingPrice"]=$data["underlying"]["last"];
									}
							}
							
					}
		// Decode the JSON response
		//echo "<pre>";
		//print_r($data);
		//echo "</pre>";
	}
	
	// Store fetched data in cache
    $dataCache[$cacheKey] = $data;
	
	return $data;
}



function fetchTickerData($ticker, $mode, $link) {
	
	
	$tickerParts = explode('_', $ticker);
    $ticker = $tickerParts[0];
	// Cache key
    $cacheKey = $ticker . "_" . $mode;
	global $dataCache;
	// Check if data is already fetched
    if (isset($dataCache[$cacheKey])) {
        return $dataCache[$cacheKey]; // Return cached data if available
    }
	//Really need to fix this part
			$date=strtotime("tomorrow");
			if(time()<strtotime("today 4:00AM")){
				$date=strtotime("yesterday 5:00PM");
			}
	if($mode==1){
	//Daily 6 month	
	$frequencytype = "daily";
	$periodtype = "month";
	$period = 6;
	$frequency ="";		
	$needExtendedHoursData="";		
	}else if($mode==2){
	//Daily 1 year	
	$frequencytype = "daily";
	$periodtype = "year";
	$period = 1;
	$frequency ="";
	$needExtendedHoursData="";		
	}else if($mode==3){
	//Weekly 1 year
	$frequencytype = "weekly";
	$periodtype = "year";
	$period = 1;
	$frequency ="";
	$needExtendedHoursData="";			
	}else if($mode==4){
	//Weekly 5 year	
	$frequencytype = "weekly";
	$periodtype = "year";
	$period = 5;
	$frequency ="";
	$needExtendedHoursData="";			
	}else if($mode==5){
	//5 min 5 day
	$frequencytype = "minute";
	$periodtype = "day";
	$period = 5;
	$frequency =5;
	$needExtendedHoursData="True";		
	}else if($mode==6){
	//10 min 5 day	
	$frequencytype = "minute";
	$periodtype = "day";
	$period = 5;
	$frequency =10;
	$needExtendedHoursData="True";			
	}else if($mode==7){
	//30 min 5 day	
	$frequencytype = "minute";
	$periodtype = "day";
	$period = 10;
	$frequency =30;
	$needExtendedHoursData="True";	
	}
	// Define time range
	$endDate = strtotime("now") * 1000; // Current time in milliseconds
	$startDate = strtotime("-$period $periodtype") * 1000; // Calculated start date
	
	$processrandomid=time().rand(0,1000)."entrytrigger";
	tdsleep($processrandomid);
	$code1 = readaddress('access_token');
	
	// Define Schwab's API endpoint and parameters
	$schwab_base_url = 'https://api.schwabapi.com/marketdata/v1/pricehistory';
	$query_params = http_build_query([
		'symbol' => $ticker,
		'periodType' => $periodtype,
		'period' => $period,
		'frequencyType' => $frequencytype,
		'frequency' => $frequency,
		'startDate' => $startDate,
		'endDate' => $endDate,
		'needExtendedHoursData' => true
	]);

	// Initialize cURL session
	$ch = curl_init("$schwab_base_url?$query_params");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	$headers = ['Authorization: Bearer ' . $code1]; // Update with the correct authorization token
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// Execute and handle the response
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	} else {
		$data = json_decode($result, true);
	}

	curl_close($ch);

	$data = json_decode($result,true);
	
	
	// Store fetched data in cache
    $dataCache[$cacheKey] = $data;
	
	return $data;
}

function arr_macd($real, $fast=12, $slow=26, $signal=9){//(array, 12, 26, 9) default
	//index [0]: MACD values
	//index [1]: Signal values
	//index [2]: Divergence values
	$arr_ema12=arr_ema($real, 12);
	$arr_ema26=arr_ema($real, 26);
		for($i = 0; $i < count($real); $i++){
			$arr_macdfastline[$i]=$arr_ema12[$i]-$arr_ema26[$i];
		}
		
		$arr_macdslowline=arr_ema($arr_macdfastline,$signal);
		for($i = 0; $i < count($real); $i++){
			$arr_macdhistogram[$i]=$arr_macdfastline[$i]-$arr_macdslowline[$i];
		}
		
	
	    return [
        'macdLine' => $arr_macdfastline, 
        'signalLine' => $arr_macdslowline, 
        'histogram' => $arr_macdhistogram
    ];
}

function arr_advanced_macd($real, $fast=12, $slow=26, $signal=9, $advancedLevel=1) {
    // Start with basic MACD calculation
    $macdData = arr_macd($real, $fast, $slow, $signal);

    // Apply advanced calculations based on the advanced level
    for ($i = 0; $i < count($real); $i++) {
        for ($j = 1; $j <= $advancedLevel; $j++) {
            $macdData['macdLine'][$i] = ema($macdData['macdLine'][$i], $fast, $real[$i]);
            $macdData['signalLine'][$i] = ema($macdData['signalLine'][$i], $signal, $macdData['macdLine'][$i]);
            $macdData['histogram'][$i] = $macdData['macdLine'][$i] - $macdData['signalLine'][$i];
        }
    }

    return $macdData;
}

function arr_advanced_ema($real, $period, $advancedLevel=1) {
    $advancedEma = arr_ema($real, $period); // Start with the real data

    for ($j = 0; $j < $advancedLevel; $j++) {
        $advancedEma = arr_ema($advancedEma, $period);
    }

    return $advancedEma;
}




function arr_advanced1macd($real, $fast=12, $slow=26, $signal=9) {
    return arr_advanced_macd($real, $fast, $slow, $signal, 1);
}

function arr_advanced2macd($real, $fast=12, $slow=26, $signal=9) {
    return arr_advanced_macd($real, $fast, $slow, $signal, 2);
}

function arr_advanced3macd($real, $fast=12, $slow=26, $signal=9) {
    return arr_advanced_macd($real, $fast, $slow, $signal, 3);
}
function findHighLowRange(&$high, &$low, $newValue) {
    if ($newValue > $high) {
        $high = $newValue;
    }
    if ($newValue < $low) {
        $low = $newValue;
    }
}

?>