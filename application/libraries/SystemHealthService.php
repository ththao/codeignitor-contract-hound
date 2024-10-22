<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * System Health Service
 *
 * @access public
 */
class SystemHealthService extends HealthService
{
	/**
	 * Notes for debug
	 */
	protected $aNotes = array();

	protected function _testTime() {
		return array('debug' => 'System Time: '.date('Y-m-d H:i:s'));
	}

	/**
	 * Test Disk Space Used Percentage
	 *
	 * @access protected
	 * @return array
	 */
	protected function _testDiskSpaceUsed() {
		$iBytesFree = disk_free_space(".");
		$iBytesTotal = disk_total_space(".");
		$fPercentage = round(($iBytesTotal-$iBytesFree)/$iBytesTotal,4)*100;

		if ($fPercentage > 80) {
			return array('success' => 0,'error' => 'High percentage used: '.$fPercentage.'%');
		}

		$this->aNotes['Percent Used'] = $fPercentage.'%';
		return array('success' => 1);
	}

	/**
	 * Test Disk Space
	 *
	 * @access protected
	 * @return array
	 */
	protected function _testDiskSpace() {
		// Free Disk Space
		$iBytesFree = disk_free_space(".");
		$aPrefix = array('B','KB','MB','GB','TB','EB','ZB','YB');
		$iBase = 1024;
		$iClass = min((int)log($iBytesFree , $iBase) , count($aPrefix) - 1);
		$sFreeSpace = sprintf('%1.2f',$iBytesFree/pow($iBase,$iClass)).' '.$aPrefix[$iClass];

		$iMinBytes = 3 * 1024 * 1024 * 1024; // 3Gigs
		if ($iBytesFree > $iMinBytes) {
			$this->aNotes['Size Free'] = $sFreeSpace;
			return array('success' => 1);
		}

		return array('success'=>0,'error'=>'Only '.$sFreeSpace.' remaining.');
	}

	/**
	 * Dump Notes
	 *
	 * @access protected
	 * @return array
	 */
	protected function _testNotes() {
		$sNotes = '';

		foreach ($this->aNotes as $sKey => $sValue) {
			$sNotes .= $sKey.' => '.$sValue.', ';
		}

		return array('debug' => trim(trim($sNotes),','));
	}
}
