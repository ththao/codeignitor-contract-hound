<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Health extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
		$this->load->library('HealthService');
	}

	public function index() {
		$aTestResults = $this->healthservice->runAllTests();
		echo json_encode($aTestResults);
	}

	public function raw() {
		$aTestResults = $this->healthservice->runAllTests();
		echo '<pre>'; var_dump($aTestResults);
	}

	public function view_log($bDelete=0) {
		$sFile = 'log-'.date('Y-m-d').'.php';
		$sFullPath = 'application/logs/'.$sFile;

		if (file_exists($sFullPath)) {
			echo '<pre>'; echo file_get_contents($sFullPath);
			if ($bDelete) {
				@unlink($sFullPath);
			}
		}
	}

	public function view_ps() {
		$sCommand = 'ps aux |grep php';
		exec($sCommand, $aOutput, $mResult);

		foreach ($aOutput as $iIndex=>$sLine) {
			if (strpos($sLine,'.php')===false) {
				unset($aOutput[$iIndex]);
			}
		}

		echo '<pre>'; var_dump($aOutput);
	}
}
