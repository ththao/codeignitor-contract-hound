#!/usr/bin/php
<?php

/*
|--------------------------------------------------------------
| CRON JOB BOOTSTRAPPER
|--------------------------------------------------------------
|
| By Jonathon Hill (http://jonathonhill.net)
| CodeIgniter forum member "compwright" (http://codeigniter.com/forums/member/60942/)
|
| Created 08/19/2008
| Version 1.2 (last updated 12/25/2008)
|
|
| PURPOSE
| -------------------------------------------------------------
| This script is designed to enable CodeIgniter controllers and functions to be easily called from the command line on UNIX/Linux systems.
|
|
| SETUP
| -------------------------------------------------------------
| 1) Place this file somewhere outside your web server's document root
| 2) Set the CRON_CI_INDEX constant to the location of your CodeIgniter index.php file
| 3) Make this file executable (chmod a+x cron.php)
| 4) You can then use this file to call any controller function:
|	./cron.php --run=/controller/method [--show-output] [--log-file=logfile] [--time-limit=N] [--server=http_server_name]
|
|
| OPTIONS
| -------------------------------------------------------------
|   --run=/controller/method   Required   The controller and method you want to run.
|   --show-output			  Optional   Display CodeIgniter's output on the console (default: don't display)
|   --log-file=logfile		 Optional   Log the date/time this was run, along with CodeIgniter's output
|   --time-limit=N			 Optional   Stop running after N seconds (default=0, no time limit)
|   --server=http_server_name  Optional   Set the $_SERVER['SERVER_NAME'] system variable (useful if your application needs to know what the server name is)
|   --secret=password		  Optional use a password to secure a cron method
|
|
| NOTE: Do not load any authentication or session libraries in controllers you want to run via cron. If you do, they probably won't run right.
|
|
| Contributions:
| -------------------------------------------------------------
|	"BDT" (http://codeigniter.com/forums/member/46597/) -- Fix for undefined constant CRON_FLUSH_BUFFERS error if the --show-output switch is not set (11/17/2008)
|	"electromute" (http://codeigniter.com/forums/member/71433/) -- Idea for [--server] commandline option (12/25/2008)
|
*/

	define('CRON_CI_INDEX', 'index.php');   // Your CodeIgniter main index.php file
	define('CRON', TRUE);   // Test for this in your controllers if you only want them accessible via cron

# Parse the command line
	$server = '';
	$run = '';
	$show_out = false;

	$script = array_shift($argv);
	$cmdline = implode(' ', $argv);
	$usage = "Usage: cron.php --run=/controller/method [--secret=password] [--show-output][-S] [--log-file=logfile] [--time-limit=N] [--server=http_server_name]\n\n";
	$required = array('--run' => FALSE);
	$bNodup = false;
	foreach($argv as $arg) {
		list($param, $value) = explode('=', $arg);
		switch($param) {
			case '--run':
				// Simulate an HTTP request
				$_SERVER['PATH_INFO'] = $value;
				$_SERVER['REQUEST_URI'] = $value;
				$run = $_SERVER['REQUEST_URI'];

				$required['--run'] = TRUE;
				break;

			case '--secret':
				$secret = $value;
				break;

			case '-S':
			case '--show-output':
				$show_out = TRUE;
				define('CRON_FLUSH_BUFFERS', TRUE);
				break;

			case '--log-file':
				if(is_writable($value)) define('CRON_LOG', $value);
				else die("Logfile $value does not exist or is not writable!\n\n");
				break;

			case '--time-limit':
				define('CRON_TIME_LIMIT', $value);
				break;

			case '--server':
				$_SERVER['SERVER_NAME'] = $value;
				$server = $value;
				break;

			case '--nodup':
				$bNodup = true;
				$iLimit = $value;
				break;

			default:
				die($usage);
		}
	}

	if (empty($server) || empty($run)) {
		die($usage);
	}

	// Prevent running two scripts at the same with the same path
	if ($bNodup) {
		// check for running cron with same path
		$cmd = 'ps aux |grep "run='.$_SERVER['PATH_INFO'].'"';

		// run the system command and assign output to a variable ($output)
		exec($cmd, $output, $result);

		// count actuall results
		$iCountFound = 0;
		foreach ($output as $line) {
			if (strpos($line,'cron.php')) {
				$iCountFound++;
			}
		}

		// check the number of lines that were returned
		if($iCountFound > $iLimit){
			// the process is still alive
			die("process already running\n");
		}
	}

	if(!defined('CRON_LOG')) define('CRON_LOG', 'cron.log');
	if(!defined('CRON_TIME_LIMIT')) define('CRON_TIME_LIMIT', 0);

	foreach($required as $arg => $present) {
		if(!$present) die($usage);
	}



# Set run time limit
	set_time_limit(CRON_TIME_LIMIT);

# Call script with curl
	if (!empty($port)) {
		$urlTo = 'http://'.$server.':'.$port.$run;
	} else {
		$urlTo = 'http://'.$server.$run;
	}

	if (isset($secret) && $secret != "") {
		if (strpos($value,'?') === false) {
			$urlTo = $urlTo."?admin_key=".$secret;
		}  else {
			$urlTo = $urlTo."&admin_key=".$secret;
		}

	}

	if ($show_out == TRUE) {
		echo "Curling: $urlTo\n";
	}

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $urlTo);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	$output = curl_exec($curl);

	if ($show_out == TRUE) {
		echo $output;
	}

	curl_close($curl);

# Log the results of this run
	//error_log("### ".date('Y-m-d H:i:s')." cron.php $cmdline\n", 3, CRON_LOG);
	//error_log($output, 3, CRON_LOG);
	//error_log("\n### \n\n", 3, CRON_LOG);


echo "\n\n";

?>