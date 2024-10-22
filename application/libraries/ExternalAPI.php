<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ExternalAPI {
	protected $_sUrl = null;
	protected $_sDomain = null;
	protected $_aMetrics = array();
	protected $_aAllAllowedKeys = array();

	public function reset() {
		$this->_sUrl = null;
		$this->_sDomain = null;
		$this->_aMetrics = array();
		return true;
	}

	public function resetData() {
		$this->_aMetrics = array();
		return true;
	}

	public function _dumpResults() {
		echo '<pre>';
		var_dump($this->_aMetrics);
	}

	public function _dump() {
		echo '<pre>';
		var_dump($this);
	}

	public function setDomain($sDomain) {
		return $this->_sDomain = $sDomain;
	}

	public function setSiteUrl($sUrl) {
		return $this->_sUrl = $sUrl;
	}

	public function run($aKeys=array()) {
		$this->resetData();

		if (empty($aKeys)) {
			$aKeys = $this->_aAllAllowedKeys;
		}

		try {
			$this->_runKeys($aKeys);
		} catch (Exception $e) {
			log_message('error','Unable to load ahref: '.$e->getMessage());
			return false;
		}

		return true;
	}

	protected function _runKeys($aKeys) {
		throw new Exception('_runKeys not overwritten.');
	}

	public function getResult($sPath) {
		if (strpos($sPath,'/') > 0) {
			$aPath = explode('/',$sPath);
			$aResults = $this->_aMetrics;

			$sValue = NULL;
			foreach ($aPath as $sPathPart) {
				if (!isset($aResults[$sPathPart])) {
					break;
				}

				$aResults = $aResults[$sPathPart];
			}

			if (is_numeric($aResults)) {
				$sValue = $aResults;
			}

			return $sValue;
		}

		if (isset($this->_aMetrics[$sPath]) && is_numeric($this->_aMetrics[$sPath])) {
			return $this->_aMetrics[$sPath];
		}

		return NULL;
	}
}
