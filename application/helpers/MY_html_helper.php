<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('link_urls')) {
	function link_urls($sText) {
		$sText = str_replace(array("\n","\r\n"),array(' \n ',' \r\n '),$sText);
		$aText = explode(' ',$sText);
		
		$sReturn = '';
		foreach ($aText as $sSection) {
			if ((strpos(strtolower($sSection),'http://') === 0 || strpos(strtolower($sSection),'https://') === 0) &&
				filter_var($sSection,FILTER_VALIDATE_URL)) {
				$sReturn .= "<a class=\"ext-link\" href=\"{$sSection}\" target=\"_blank\">{$sSection}</a> ";
			} else {
				$sReturn .= $sSection.' ';
			}
		}
		
		$sReturn = str_replace(array(' \n ',' \r\n '),array("\n","\r\n"),$sReturn);
		return trim($sReturn);
	}
}