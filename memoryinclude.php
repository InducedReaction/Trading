<?php
// Create 10000 byte shared memory block with system id of 0xff3
//Save data as json to memory.
//Then when we write data, we should be able to write to each variable
ini_set('max_execution_time', '0');

$defaultstringsize=10000;
//$processrandomid=time().rand(0,1000)."AB";

//tdsleep($processrandomid);
include_once 'library.php';




function readdata(){
	//$defaultstringsize=10000;
	//$shm_id = shmop_open(0xff3, "c", 0644, $defaultstringsize);
	// Get shared memory block's size
	//$shm_size = shmop_size($shm_id);

	// Now lets read the string back
	//$my_string = shmop_read($shm_id, 0, $shm_size);
	$link = connectdb();
	$query=mysqli_query($link, "SELECT * FROM `gobrrr`.`td` WHERE `id` = 1 ");
	$row=mysqli_fetch_array($query);
	$my_string = $row['value'];
	
	
	
	$pieces = explode("</end>", $my_string);
	return $pieces[0];
}
	
function writedata($string){
	//$defaultstringsize=10000;
	//$shm_id = shmop_open(0xff3, "c", 0644, $defaultstringsize);
	// Lets write a test string into shared memory
	//$shm_bytes_written = shmop_write($shm_id, $string."</end>", 0);
	$link = connectdb();
	$query=mysqli_query($link, "UPDATE `gobrrr`.`td` SET `value` = '$string' WHERE `td`.`id` = 1;");
	//$row=mysqli_fetch_array($query);
	//$my_string = $row['value'];
}
	

function readaddress($address){
	//$defaultstringsize=10000;
	//$shm_id = shmop_open(0xff3, "c", 0644, $defaultstringsize);
	// Get shared memory block's size
	//$shm_size = shmop_size($shm_id);

	// Now lets read the string back
	//$my_string = shmop_read($shm_id, 0, $shm_size);
	$link = connectdb();
	$query=mysqli_query($link, "SELECT * FROM `gobrrr`.`td` WHERE `id` = 1 ");
	$row=mysqli_fetch_array($query);
	$my_string = $row['value'];
	$pieces = explode("</end>", $my_string);
	
	$data = json_decode($pieces[0], true);
	
		if(isset($data[$address])){
			return $data[$address];
		}else{
			return false;
		}
		
}
	
function writetoaddress($variable,$string){//string that goes in the field, field name
	//$defaultstringsize=10000;
	//$shm_id = shmop_open(0xff3, "c", 0644, $defaultstringsize);
	// Get shared memory block's size
	//$shm_size = shmop_size($shm_id);

	// Now lets read the string back
	//$my_string = shmop_read($shm_id, 0, $shm_size);
	$link = connectdb();
	$query=mysqli_query($link, "SELECT * FROM `gobrrr`.`td` WHERE `id` = 1 ");
	$row=mysqli_fetch_array($query);
	$my_string = $row['value'];
	
	
	
	$pieces = explode("</end>", $my_string);
	
	//get the string and decode it from json to get array
	$data = json_decode($pieces[0], true);
	
	$data[$variable]=$string;
	
	writedata(json_encode($data));			
}
	
	



//Tells how much to sleep and queue
function tdsleep($processid){
	
	if(readaddress("killscripts")=="1"){
		die("Kill all scripts!");
	}
		
		
	//start off by checking the last time it got pinged
	
	//if the last time it got pinged was more than a half second ago and there is no one in line, don't sleep
	if(readaddress("lastping")+.51<microtime(true)&&readaddress("pingqueue")==""){
		//update last time pinged
		writetoaddress("lastping", microtime(true));
		
	}
		
	
	//if the last time it got pinged was less than half a second ago and there is no line, get in line and sleep the thread
	if(readaddress("lastping")+.51>microtime(true)&&readaddress("pingqueue")==""){
				
		//get in line stuff
		//add $processid, to the end of the queue.
		$newpingqueue=readaddress("pingqueue").$processid.",";
		writetoaddress("pingqueue", $newpingqueue);
		
		//sleep till half second has past since last ping,
		$sleeptime=1000000*(readaddress("lastping")+.51-microtime(true));
		//usleep(10000);//2 seconds
		if($sleeptime>0){
			usleep($sleeptime);
		}
		
		//update last time pinged
		writetoaddress("lastping", microtime(true));
		//remove $processid, from beginning of line
		$newpingqueue=str_replace($processid.",","",readaddress("pingqueue"));
		writetoaddress("pingqueue", $newpingqueue);
	}
		
	
	//if there is a line,
	if(readaddress("pingqueue")!=""){
				
		//add $processid, to the end of the queue.
		$newpingqueue=readaddress("pingqueue").$processid.",";
		writetoaddress("pingqueue", $newpingqueue);
		
		$nextinline= explode(",", readaddress("pingqueue"));
		
		// Add a new segment right before the existing while loop to check if next in line is jammin 
			$initialTime = microtime(true);
			$hasBeenRemoved = false;

			while($processid != $nextinline[0] && readaddress("pingqueue") != "") {
				$currentTime = microtime(true);
				$timeElapsed = $currentTime - $initialTime;

				if($timeElapsed > 0.5) { // If more than half a second has passed.
					$currentQueue = readaddress("pingqueue");
					$currentFrontProcess = explode(",", $currentQueue)[0];

					// Check if the process at the front is still the same.
					if($currentFrontProcess == $nextinline[0]) {
						// Remove the process from the front of the queue.
						$newpingqueue = preg_replace('/^'.preg_quote($processid, '/').'\,/', '', $currentQueue, 1);
						writetoaddress("pingqueue", $newpingqueue);
						$hasBeenRemoved = true;
						break; // Exit the while loop since the queue is now updated.
					}

					$initialTime = $currentTime; // Reset the initial time for the next interval.
				}

				if($hasBeenRemoved) {
					break; // Exit the while loop since we've removed the stuck process.
				}

				// Existing code to sleep for a calculated time.
				$sleeptime = 1000000 * (readaddress("lastping") + 0.51 - $currentTime);
				if($sleeptime > 0 && $sleeptime < 10000){
					usleep($sleeptime);
				} else {
					usleep(510000);
				}
				
				// Refresh the next in line after sleeping.
				$nextinline = explode(",", readaddress("pingqueue"));
			}
			//end of the new segment
		//while($processid!=nextinline)
				while($processid!=$nextinline[0]&&readaddress("pingqueue")!=""){//wait in line till it's your turn//added additional variable to prevent a lost ping
									if(readaddress("killscripts")=="1"){
									die("Kill all scripts!");
									}
					//echo "<br>Looping";
					//echo "<br>Next in line: ".$nextinline[0];
					//echo " -- Process id: ".$processid;
					//flush here
								//ob_flush();
								//flush();
					//count the commas and sleep for that much time
					//sleep till half second has past since last ping,
					$sleeptime=1000000*(readaddress("lastping")+.51-microtime(true));
					if($sleeptime>0&&$sleeptime<10000){
						usleep($sleeptime);
					}else{
						usleep(510000);
					}
					//considering adding a delete next in line right here if the next in line hasn't changed since last check.
					if($nextinline==explode(",", readaddress("pingqueue"))){
						//This guy still in line?
					}
					$nextinline= explode(",", readaddress("pingqueue"));
				}
		//It's your turn at this point, but you still need to wait for half second after last ping	
		//sleep till half second has past since last ping,
		$sleeptime=1000000*(readaddress("lastping")+.51-microtime(true));
		//usleep(10000);//2 seconds
		if($sleeptime>0){
			usleep($sleeptime);
		}
		
		
		//update last time pinged
		writetoaddress("lastping", microtime(true));
		//remove $processid, from beginning of line
		$newpingqueue=str_replace($processid.",","",readaddress("pingqueue"));
		writetoaddress("pingqueue", $newpingqueue);
		
	}
}

if(""==readaddress("accountnumber")){
	
	//Initiate global variables stored in shared memory
	$data = [0,0];
	writedata(json_encode($data));
	//$time_start = microtime(true);
	//writetoaddress("access_token", $code1);//access_token=$code1;
	writetoaddress("lastping", "0");
	writetoaddress("pingqueue", "");
	writetoaddress("gobrrr", "0");
	writetoaddress("killscripts", "0");
	writetoaddress("lastalgoping", "0");

	
	writetoaddress("etrade_consumer_key", "etrade consumer key goes here");
    writetoaddress("etrade_consumer_secret", "etrade consumber secret goes here");
    writetoaddress("etrade_oauth_token","");
    writetoaddress("etrade_oauth_token_secret","");
	
	
	
	
}
?>