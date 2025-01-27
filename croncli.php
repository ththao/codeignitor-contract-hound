#!/usr/bin/php
<?php
/**
 * Lockdown/Lockout all cron jobs without override
 */
$bLockdown = false;


/**
 * Dotenv
 */

require_once(__DIR__.'/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

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
|    ./cron.php --run=/controller/method [--show-output] [--log-file=logfile] [--time-limit=N] [--server=http_server_name]
|
|
| OPTIONS
| -------------------------------------------------------------
|   --run=/controller/method   Required   The controller and method you want to run.
|   --show-output              Optional   Display CodeIgniter's output on the console (default: don't display)
|   --log-file=logfile         Optional   Log the date/time this was run, along with CodeIgniter's output
|   --time-limit=N             Optional   Stop running after N seconds (default=0, no time limit)
|   --server=http_server_name  Optional   Set the $_SERVER['SERVER_NAME'] system variable (useful if your application needs to know what the server name is)
|
|
| NOTE: Do not load any authentication or session libraries in controllers you want to run via cron. If you do, they probably won't run right.
|
|
| Contributions:
| -------------------------------------------------------------
|    "BDT" (http://codeigniter.com/forums/member/46597/) -- Fix for undefined constant CRON_FLUSH_BUFFERS error if the --show-output switch is not set (11/17/2008)
|    "electromute" (http://codeigniter.com/forums/member/71433/) -- Idea for [--server] commandline option (12/25/2008)
|
*/

	define('CRON_CI_INDEX', '/jet/app/www/default/index.php');   // Your CodeIgniter main index.php file
	define('CRON', TRUE);   // Test for this in your controllers if you only want them accessible via cron

# Parse the command line
	$script = array_shift($argv);
	$cmdline = implode(' ', $argv);
	$usage = "Usage: croncli.php --run=/controller/method [--show-output][-S] [--log-file=logfile] [--time-limit=N] [--server=http_server_name]\n\n";
	$required = array('--run' => FALSE);
	$bNodup = false;
	$iAllowedCount = 0;
	$bOverride = false;

	$_GET['admin_key'] = $_ENV['ADMIN_KEY'];
	$_SERVER['HTTP_HOST'] = $_ENV['HOST'];
	$_SERVER['SERVER_NAME'] = $_ENV['HOST'];
	$_SERVER['SERVER_PORT'] = $_ENV['HOST_PORT'];
	$_SERVER['QUERY_STRING'] = "admin_key=".$_ENV['ADMIN_KEY'];
	$_SERVER['REQUEST_METHOD'] = "GET";
	$_SERVER['CI_ENV'] = $_ENV['CI_ENV'];
	putenv("HOME=" . __DIR__);

	foreach($argv as $arg)
	{
		list($param, $value) = explode('=', $arg);
		switch($param)
		{
			case '--run':
				// Simulate an HTTP request
				$_SERVER['PATH_INFO'] = $value;
				$_SERVER['REQUEST_URI'] = $value;

				// URI patch (_parse_cli_args()) for CodeIgniter Reactor 2.0.2
				unset($_SERVER['argv']);
				$_SERVER['argv'][0] = null;
				$_SERVER['argv'][1] = $value;

				$required['--run'] = TRUE;
				break;

			case '-S':
			case '--show-output':
				define('CRON_FLUSH_BUFFERS', TRUE);
				break;

			case '--log-file':
				if (is_writable($value))
					define('CRON_LOG', $value);
				else
					die("Logfile $value does not exist or is not writable!\n\n");
				break;

			case '--time-limit':
				define('CRON_TIME_LIMIT', $value);
				break;

			case '--server':
				$_SERVER['SERVER_NAME'] = $value;
				break;

			case '--nodup':
				$bNodup = true;
				$iAllowedCount = $value;
				break;

			case '--override':
				$bOverride = true;
				break;

			case '--secret':
				$_GET['admin_key'] = $value;
				break;
		   default:

				die($usage);
		}
	}

	if ($bLockdown && !$bOverride) {
		exit();
	}

	// Prevent running two scripts at the same with the same path
	if ($bNodup) {
		// check for running cron with same path
		$cmd = 'ps aux |grep "run='.$_SERVER['PATH_INFO'].'"';

		$iMyPid = getmypid();
		exec($cmd, $output, $result);
		$aProcesses = array();
		foreach ($output as $sProcess) {
			$sProcess = preg_replace('/\s+/',' ',$sProcess);
			$aLine = explode(" ",$sProcess);
			$aProcesses[] = array(
				'pid' => $aLine[1]
				,'process' => implode(' ',array_slice($aLine,10))
			);

		}

		$iCountFound = 0;
		foreach ($aProcesses as $aProcess) {
			if (strpos($aProcess['process'],__FILE__)===false || $aProcess['pid'] == $iMyPid) {
				continue;
			}

			$iCountFound++;
		}
		// check the number of lines that were returned
		if($iCountFound >= $iAllowedCount){
			// the process is still alive
			die("process already running\n");
		}
	}

	if(!defined('CRON_LOG')) define('CRON_LOG', 'cron.log');
	if(!defined('CRON_TIME_LIMIT')) define('CRON_TIME_LIMIT', 0);

	foreach($required as $arg => $present)
	{
		if(!$present) die($usage);
	}



# Set run time limit
	set_time_limit(CRON_TIME_LIMIT);

	//@file_put_contents('/jet/app/www/default/application/logs/log-'.date('Y-m-d').'.php','REQUIRED - '.date('Y-m-d H:i:s').": cron calling '{$_SERVER['REQUEST_URI']}'\n",FILE_APPEND);

# Run CI and capture the output
	ob_start();

	chdir(dirname(CRON_CI_INDEX));
	require(CRON_CI_INDEX);           // Main CI index.php file
	//$output = ob_get_contents();

	if(defined('CRON_FLUSH_BUFFERS')) {
		while(@ob_end_flush());        // display buffer contents
	} else {
		//$sContent = @ob_get_contents();
		//@file_put_contents('/jet/app/www/default/var/cron/log-'.date('Y-m-d_H_i_s_u').'.php','<?php exit;'."\n".$sContent,FILE_APPEND);
		ob_end_clean();
	}


# Log the results of this run
	//error_log("### ".date('Y-m-d H:i:s')." cron.php $cmdline\n", 3, CRON_LOG);
	//error_log($output, 3, CRON_LOG);
	//error_log("\n### \n\n", 3, CRON_LOG);


echo "\n\n";

?>
