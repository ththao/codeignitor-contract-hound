<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/models/MemberAccessLogModel.php');
/**
 * Member Access Log Service Class
 *
 * @access public
 */
class MemberAccessLogService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'MemberAccessLogModel';

	///////////////////////////////////////////////////////////////////////////
	/////  Class Methods   ///////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
	}

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add Member Access Log
	 *
	 * @access public
	 * @param MemberAccessLogModel $oMemberAccessLog
	 * @return ServiceResponse
	 */
	public function addMemberAccessLog(MemberAccessLogModel $oMemberAccessLog) {
		$oMemberAccessLog->create_date = date('Y-m-d H:i:s');
		$iResult = $this->_getModel('member_access_log_m')->addItem($oMemberAccessLog->toArray());

		if ($iResult) {
			$oMemberAccessLog->member_access_log_id = $iResult;
			$oMemberAccessLog->isSaved(true);
			return new ServiceResponse(array($oMemberAccessLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Member Access Log
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteMemberAccessLog($aFilters) {
		$bResponse = $this->_getModel('member_access_log_m')->deleteItems($aFilters);

		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Member Access Log
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getMemberAccessLog($aFilters=array()) {
		$aMemberAccessLog = $this->_getModel('member_access_log_m')->getItem($aFilters);

		if (!empty($aMemberAccessLog)) {
			return $this->_setupResponse(array($aMemberAccessLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Member Access Logs
	 *
	 * @access public
	 * @param array $aFilters
	 * @param string $sOrderBy (Optional, 'create_date desc')
	 * @param integer $iLimit (Optional)
	 * @param integer $iOffset (50, Optional)
	 * @return ServiceResponse
	 */
	public function getMemberAccessLogs($aFilters=array(),$sOrderBy='create_date desc',$iLimit=50,$iOffset=0) {
		$aMemberAccessLogs = $this->_getModel('member_access_log_m')->getItems($aFilters,$sOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aMemberAccessLogs);
	}

	/**
	 * Get Last Member Access Log
	 *
	 * @access public
	 * @param integer $iMemberId
	 * @return ServiceResponse
	 */
	public function getLastMemberAccessLog($iMemberId) {
		if (empty($iMemberId)) {
			return new MemberAccessLogModel();
		}

		$aMemberAccessLog = $this->_getModel('member_access_log_m')->getItems(array(
			'member_id' => $iMemberId
		),'create_date desc',1);

		if (!empty($aMemberAccessLog)) {
			return $this->_setupResponse($aMemberAccessLog);
		}

		return new ServiceResponse(array(new MemberAccessLogModel()));
	}

	/**
	 * Update Member Access Log
	 *
	 * @access public
	 * @param MemberAccessLogModel $oMemberAccessLog
	 * @return ServiceResponse
	 */
	public function updateMemberAccessLog(MemberAccessLogModel $oMemberAccessLog) {
		$bResponse = $this->_getModel('member_access_log_m')->updateItem($oMemberAccessLog->toArray());
		if ($bResponse) {
			$oMemberAccessLog->isSaved(true);
			return new ServiceResponse(array($oMemberAccessLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
