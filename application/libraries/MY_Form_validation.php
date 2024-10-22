<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

class MY_Form_validation extends CI_Form_validation
{
	var $_error_prefix			= '<p>';

	function __construct($rules = array())
	{
		parent::__construct($rules);
		$this->CI->load->language('extra_validation');
	}

	function fieldValue($sField,$mDefault=null) {
		if (!is_string($sField) || !isset($this->_field_data[$sField])) {
			return $mDefault;
		}

		return $this->_field_data[$sField]['postdata'];
	}

	/**
	 * Alpha-numeric with underscores dots and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function alpha_dot_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_\-\.])+$/i", $str)) ? FALSE : TRUE;
	}

	/**
	 * No Html
	 *
	 * @access public
	 * @param string $str
	 * @return boolean
	 */
	function no_html($str) {
		$sStrippedStr = strip_tags($str);
		return strcmp($sStrippedStr,$str)===0;
	}

	/**
	 * No Html Except Links
	 *
	 * @access public
	 * @param string $str
	 * @return boolean
	 */
	function no_html_except_links($str) {
		$sStrippedStr = strip_tags($str,'a');
		return strcmp($sStrippedStr,$str)===0;
	}

	function clean_spaces($str) {
		$str = preg_replace('/[ ]+/',' ',$str);
		return $str;
	}

	/**
	 * Formats an UTF-8 string and removes potential harmful characters
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 * @author	Jeroen v.d. Gulik
	 * @since	v1.0-beta1
	 * @todo	Find decent regex to check utf-8 strings for harmful characters
	 */
	function utf8($str)
	{
		// If they don't have mbstring enabled (suckers) then we'll have to do with what we got
		if ( ! function_exists($str))
		{
			return $str;
		}

		$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');

		return htmlentities($str, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Array Has Entries
	 *
	 * @access public
	 * @param array $arr
	 * @return boolean
	 */
	function array_has_entries($arr) {
		if (!is_array($arr) || empty($arr)) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Array Entries are Numeric
	 *
	 * @access public
	 * @param array $arr
	 * @return boolean
	 */
	function array_entries_numeric($arr) {
		foreach($arr as $value) {
			if (!is_numeric($value)) {
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Valid Date
	 *
	 * @access public
	 * @param string $sDate
	 * @param string $sFormat
	 * @return boolean
	 */
	function valid_date($sDate,$sFormat) {
		if (empty($sFormat)) {
			$sFormat = 'Y-m-d H:i:s';
		}

		$iDate = strtotime($sDate);
		$sFormattedDate = date($sFormat,$iDate);

		if (strcmp($sDate,$sFormattedDate) === 0) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Card Number Clean
	 *
	 * @access public
	 * @param string $number
	 * @return boolean
	 */
	function card_number_clean($number) {
		return preg_replace("/[^0-9]/", "", $number);
	}

	/**
	 * trim the str
	 *
	 * @access public
	 * @param string $str
	 * @return string
	 */
	function trim($str) {
		return trim($str);
	}

	function lower($str) {
		return strtolower($str);
	}

	/**
	 * Empty to zero
	 *
	 * @access public
	 * @param integer $number
	 * @return integer
	 */
	function empty_to_zero($number) {
		if (empty($number)) {
			return 0;
		}

		return $number;
	}

	/**
	 * Empty to null
	 *
	 * @access public
	 * @param integer $number
	 * @return integer
	 */
	function empty_to_null($number) {
		if (empty($number)) {
			return NULL;
		}

		return $number;
	}

	/**
	 * Value can not be over max
	 *
	 * @param string $str
	 * @param string $max
	 *
	 * @return TRUE|FALSE
	 */
	function max_value($str,$max) {
		if ($str <= $max) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Value can not be under min
	 *
	 * @param string $str
	 * @param string $min
	 *
	 * @return TRUE|FALSE
	 */
	function min_value($str,$min) {
		if ($str >= $min) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Valid Credit Card Number
	 *
	 * @access public
	 * @param string $card_number
	 * @return boolean
	 */
	function card_number_valid($card_number) {
		$card_number = strrev($this->card_number_clean($card_number));
		$sum = 0;

		for ($i = 0; $i < strlen($card_number); $i++) {
		$digit = substr($card_number, $i, 1);

			// Double every second digit
			if ($i % 2 == 1) {
			$digit *= 2;
			}

			// Add digits of 2-digit numbers together
			if ($digit > 9)    {
			$digit = ($digit % 10) + floor($digit / 10);
			}

			$sum += $digit;
		}

		// If the total has no remainder it's OK
		return ($sum % 10 == 0) ? TRUE : FALSE;
	}

	/**
	 * Validate the credit cards expire date
	 *
	 * @access public
	 * @param mixed $year
	 * @param mixed $month_field
	 * @return bool
	 */
	public function credit_card_expire($year,$month_field) {
		if (!isset($this->_field_data[$month_field]['postdata'])) {
			$month = '';
			foreach ($this->_field_data as $field_data) {
				if (!empty($field_data['external_key']) && $month_field == $field_data['external_key']) {
					$month = $field_data['postdata'];
				}
			}
		} else {
			$month = $this->_field_data[$month_field]['postdata'];
		}

		if (empty($month)) {
			return FALSE;
		}

		$expire_date = mktime(0, 0, 0, ($month + 1), 1, $year);
		return ($expire_date > time()) ? TRUE : FALSE;
	}

	/**
	 * If other field has data, this field is required
	 *
	 * @access	public
	 * @param	str $str (value for current field)
	 * @param	str $field (field name for other)
	 * @return	bool
	 */
	function also_if($str, $field) {
		if (!isset($_POST[$field])) {
			return FALSE;
		}

		if (empty($_POST[$field])) {
			return TRUE;
		}

		return empty($str) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate URL
	 *
	 * @access    public
	 * @param    string
	 * @return    string
	 */
	function valid_url($url) {
		if (!filter_var($url,FILTER_VALIDATE_URL)) {
			return false;
		}

		$pattern = "/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
		return (bool) preg_match($pattern, $url);
	}

	/**
	 * Add Http if necessary for Url
	 *
	 * @access public
	 * @param string
	 * @return string
	 */
	function append_http($url) {
		if (strpos($url,'http://')===false && strpos($url,'https://')===false) {
			return 'http://'.$url;
		}

		return $url;
	}

	// --------------------------------------------------------------------

	/**
	 * Clean trailing slashes
	 *
	 * @access public
	 * @param $url
	 * @return string
	 */
	function clean_slash($url) {
		return rtrim($url,'/');
	}

	/**
	 * Real URL
	 *
	 * @access    public
	 * @param    string
	 * @return    string
	 */
	function real_url($url) {
		return @fsockopen("$url", 80, $errno, $errstr, 30);
	}

	function is_boolean($value) {
		return is_bool($value);
	}

	/**
	 * Get First Error Message
	 *
	 * @access public
	 * @return string
	 */
	function first_error() {
		if (empty($this->_error_array) || !is_array($this->_error_array)) {
			return null;
		}

		return reset($this->_error_array);
	}

	public function set_error($sField,$sError) {
		return $this->_error_array[$sField] = $sError;
	}

	function first_error_field() {
		if (empty($this->_error_array) || !is_array($this->_error_array)) {
			return null;
		}

		return key($this->_error_array);
	}

	/**
	 * Validate Country Code
	 *
	 * @access public
	 * @param $country
	 * @return bool
	 */
	public function valid_country($country) {
		$countries = ConfigService::getItem('country_list');
		if (!is_string($country) || !isset($countries[$country])) {
			return false;
		}

		return true;
	}
}
/* End of file MY_Form_validation.php */
