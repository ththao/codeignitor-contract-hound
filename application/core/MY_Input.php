<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Customization of input class
 *
 * @access public
 */
class MY_Input extends CI_Input {

	/**
	 * Find input by key else use default
	 *
	 * @access public
	 * @param mixed $mIndex
	 * @param mixed $mDefault  (Optional, Default false)
	 * @return mixed
	 */
	public function my_get($mIndex,$mDefault=false) {
		$mValue = $mDefault;

		if (isset($_GET[$mIndex])) {
			$mValue = $this->get($mIndex,$this->_enable_xss);
		}

		return is_array($mValue)?$mValue:trim($mValue);
	}

	/**
	 * Find input by key else use default
	 *   first post then get
	 *
	 * @access public
	 * @param mixed $mIndex
	 * @param mixed $mDefault  (Optional, Default false)
	 * @return mixed
	 */
	public function my_get_post($mIndex,$mDefault=false) {
		$mValue = $mDefault;

		if (isset($_POST[$mIndex])) {
			$mValue = $this->post($mIndex,$this->_enable_xss);
		} elseif (isset($_GET[$mIndex])) {
			$mValue = $this->get($mIndex,$this->_enable_xss);
		}

		return is_array($mValue)?$mValue:trim($mValue);
	}

	/**
	 * Find input by key else use default
	 *
	 * @access public
	 * @param mixed $mIndex
	 * @param mixed $mDefault  (Optional, Default false)
	 * @return mixed
	 */
	public function my_post($mIndex,$mDefault=false) {
		$mValue = $mDefault;

		if (isset($_POST[$mIndex])) {
			$mValue = $this->post($mIndex,$this->_enable_xss);
		}

		return is_array($mValue)?$mValue:trim($mValue);
	}
}