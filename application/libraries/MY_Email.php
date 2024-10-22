<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_Email - Allows for email config settings to be stored in the db.
 *
 * @author      Stephen Cozart
 * @author		PyroCMS Dev Team
 * @package 	PyroCMS\Core\Libraries
 */
class MY_Email extends CI_Email {

	/**
	 * Constructor method
	 *
	 * @access public
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		//set a few config items (duh)
		$config['mailtype']	= "html";
		$config['charset']	= "utf-8";
		$config['crlf']		= "\r\n";
		$config['newline']	= "\r\n";

		$this->initialize($config);
	}

	/**
	 * Update the send config
	 *
	 * @access public
	 * @param array $aConfig
	 * @return void
	 */
	public function updateConfig($aConfig=array()) {
		foreach ($aConfig as $key => $val) {
			if (isset($this->$key)) {
				$method = 'set_'.$key;

				if (method_exists($this, $method)) {
					$this->$method($val);
					//log_message('required','updateConfig: method '.$method.' '.$val);
				} else {
					$this->$key = $val;
					//log_message('required','updateConfig: key '.$key.' '.$val);
				}
			}
		}

		//log_message('required','updateconfig check: '.$this->smtp_port);
	}
}
/* End of file MY_Email.php */
