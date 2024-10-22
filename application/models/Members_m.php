<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Members_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'members';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'member_id';

	public function addFailedLoginAttempt($iMemberId) {
		$sDB = $this->sDB;
		$aParams = array($iMemberId);
		$sQuery = "update members set count_failed_login_attempts = count_failed_login_attempts + 1 where member_id = ?";
		return $this->$sDB->query($sQuery,$aParams);
	}

	public function resetFailedLoginAttempt($iMemberId) {
		$sDB = $this->sDB;
		$aParams = array($iMemberId);
		$sQuery = "update members set count_failed_login_attempts = 0 where member_id = ?";
		return $this->$sDB->query($sQuery,$aParams);
	}
} 