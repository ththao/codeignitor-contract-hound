<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
define('EXT','.php');
class MY_Log extends CI_Log {
	var $_levels = array(
		 'REQUIRED' => '0'
		,'ERROR'    => '1'
		,'DEBUG'    => '2'
		,'INFO'     => '3'
		,'ALL'      => '4'
	);

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param       string  the error level
	 * @param       string  the error message
	 * @param       bool    whether the error is a native PHP error
	 * @return      bool
	 */
	public function write_log($level = 'error', $msg, $php_error = FALSE) {
		if ($this->_enabled === FALSE) {
			return FALSE;
		}

		$level = strtoupper($level);

		if (!isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold)) {
			return FALSE;
		}

		$filepath = $this->_log_path.'log-'.date('Y-m-d').EXT;
		$message  = '';

		if (!is_writable($this->_log_path)) {
			//throw new Exception('unable to write to log dir');
			return FALSE;
		}

		if (!file_exists($filepath)) {
			@touch($filepath);
			@chmod($filepath, 0777);
			$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}

		if (!is_writable($filepath)) {
			//throw new Exception('not writable log file');
			return FALSE;
		}

		if (!$fp = @fopen($filepath, 'a+')) {
			//throw new Exception('unable to open log file: '.$filepath.' '.$msg);
			return FALSE;
		}

		$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;
	}
}