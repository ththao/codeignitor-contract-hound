<?php defined('BASEPATH') OR exit('No direct script access allowed');
function get_model($sModelClassName,$aValues=array()) {
	$aFiles = glob('application/models/*Model.php');
	$aClasses = array();
	foreach ($aFiles as $sFile) {
		$sBaseFileName = basename($sFile);
		$sMinName = strtolower(str_replace('.php','',$sBaseFileName));
		$aClasses[$sMinName] = array(
			'filepath' => $sFile
			,'file'    => $sBaseFileName
			,'proper'  => str_replace('.php','',$sBaseFileName)
		);
	}

	$sMinModelClassName = strtolower($sModelClassName);
	if (empty($aClasses[$sMinModelClassName])) {
		return new GenericModel($aValues,$sMinModelClassName);
	}

	require_once($aClasses[$sMinModelClassName]['filepath']);
	$sClassName = $aClasses[$sMinModelClassName]['proper'];
	return new $sClassName($aValues);
}
