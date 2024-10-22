<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Health Service
 *
 * @access public
 */
class HealthService
{
	/**
	 * Init Testing
	 *
	 * @access protected
	 * @return void
	 */
	protected function initTesting() {
		// for child classes
	}

	/**
	 * Run Tests
	 *
	 * @access public
	 * @return array
	 */
	public function runTests() {
		$aMethods = get_class_methods($this);

		$this->initTesting();

		$aDiagStats = array();
		foreach ($aMethods as $sMethod) {
			if (strpos($sMethod,'_test')===0) {
				$sKey = str_replace('_test','',$sMethod);
				$aDiagStats[$sKey] = $this->$sMethod();
			}
		}

		return $aDiagStats;
	}

	/**
	 * Run Tests
	 *
	 * @access public
	 * @return array
	 */
	public function runAllTests() {
		$sCurrentClassName = get_class($this);
		if (strcmp($sCurrentClassName,'HealthService') !== 0) {
			return false;
		}

		$sDir = __DIR__;
		$aTestServices = glob($sDir.'/*HealthService.php');

		if (empty($aTestServices)) {
			return array();
		}

		$aResults = array();

		foreach ($aTestServices as $sTestService) {
			$sClassName = basename($sTestService,'.php');

			if (strcmp($sClassName,'HealthService')===0) {
				continue;
			}

			require_once($sTestService);

			if (!class_exists($sClassName)) {
				continue;
			}

			$oClass = new $sClassName();

			$sArea = str_replace('HealthService','',$sClassName);
			$aResults[$sArea] = $oClass->runTests();
		}

		return $aResults;
	}
}
