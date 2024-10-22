<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

/**
 * Echo and escape userdata
 *
 * @access public
 * @param string $string
 * @param integer $iMaxLength (Optional)
 * @return boolean
 */
function echud($string,$iMaxLength=false) {
	echo retud($string,$iMaxLength);
	return true;
}

/**
 * escape userdata
 *
 * @access public
 * @param string $string
 * @param integer $iMaxLength (Optional)
 * @return boolean
 */
function retud($string,$iMaxLength=false) {
	if ($iMaxLength && $iMaxLength > 3 && strlen($string) > $iMaxLength) {
	  $string = substr($string,0,$iMaxLength-3);
	  $string .= '...';
	}

	return str_replace(array('{', '}','<','>','"'), array('&#123;', '&#125;','&lt;','&gt;','&quot;'), $string);
}

function anchors_only($string) {
	echo str_replace(array('{', '}'), array('&#123;', '&#125;'), strip_tags($string,'<a>'));
}

function days_diff($sExpireDate) {
	$iNow = time(); // or your date as well
	$iExpireDate = strtotime($sExpireDate);
	$iDateDiff = abs($iNow - $iExpireDate);
	return floor($iDateDiff/(60*60*24));
}