<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ConfigService {
	static $aConfig = array();
	static $bLoaded = false;
	static $sSuffix = '';
	static $aConfigFilesLoaded = array();

	public static function preLoad() {
		if (self::$bLoaded) {
			return true;
		}

		if (empty(self::$sSuffix)) {
			self::setLive();
			if (defined('ENVIRONMENT') && ENVIRONMENT == 'development') {
				self::setDev();
			}
		}

		if (!defined('CONFIGPATH') || !is_dir(CONFIGPATH)) {
			echo "ERROR: Not Defined: ".CONFIGPATH;
			throw new Exception("ERROR: Not Defined: ".CONFIGPATH);
			return false;
		}

		if (file_exists(CONFIGPATH.'autoload.php')) {
			require_once(CONFIGPATH.'autoload.php');
			if (!empty($autoload['config'])) {
				$aConfigFiles = $autoload['config'];
				$autoload = null;

				foreach ($aConfigFiles as $sConfig) {
					$sConfig = strtolower($sConfig);

					if (in_array($sConfig,self::$aConfigFilesLoaded)) {
						continue;
					}

					$config = null;
					if (file_exists(CONFIGPATH.$sConfig.'.php')) {
						require(CONFIGPATH.$sConfig.'.php');
						if (isset($config) && is_array($config)) {
							self::$aConfig = array_merge(self::$aConfig,$config);
						}
						self::$aConfigFilesLoaded[] = $sConfig;
					}
				}
			}
		}

		if (file_exists(CONFIGPATH.'config.php')) {
			$config = null;

			require(CONFIGPATH.'config.php');
			if (isset($config) && is_array($config)) {
				self::$aConfig = array_merge(self::$aConfig,$config);
				self::$aConfigFilesLoaded[] = 'config';
			}
		}

		self::$bLoaded = true;
		return true;
	}

	public static function loadFile($sFile=false) {
		$sFile = strtolower($sFile);
		if (in_array($sFile,self::$aConfigFilesLoaded)) {
			return true;
		}

		if (file_exists(CONFIGPATH.$sFile.'.php')) {
			require(CONFIGPATH.$sFile.'.php');
			if (isset($config) && is_array($config)) {
				self::$aConfig = array_merge(self::$aConfig,$config);
				return true;
			}
		}

		throw new Exception('Config file not found: '.CONFIGPATH.$sFile.'.php');
		return false;
	}

	public static function getItem($sItem) {
		self::preLoad();

		if (isset(self::$aConfig[$sItem.self::$sSuffix])) {
			return self::$aConfig[$sItem.self::$sSuffix];
		}

		if (!isset(self::$aConfig[$sItem])) {
			return null;
		}

		return self::$aConfig[$sItem];
	}

	public static function setItem($sItem,$sValue) {
		self::preLoad();
		return self::$aConfig[$sItem] = $sValue;
	}

	public static function dumpConfig() {
		self::preLoad();
		var_dump(self::$aConfig);
		return true;
	}

	public static function isLive() {
		return self::$sSuffix == '_live';
	}

	public static function setLive() {
		return self::$sSuffix = '_live';
	}

	public static function setDev() {
		return self::$sSuffix = '_dev';
	}
}
