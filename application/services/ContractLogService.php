<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ContractLogService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractLogModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add ContractLog
	 *
	 * @access public
	 * @param ContractLogModel $oContractLog
	 * @return ServiceResponse
	 */
	public function addContractLog(ContractLogModel $oContractLog) {
		$iResult = $this->_getModel('contract_logs_m')->addItem($oContractLog->toArray());
		if ($iResult) {
			$oContractLog->contract_log_id = $iResult;
			$oContractLog->isSaved(true);
			return new ServiceResponse(array($oContractLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete ContractLogs
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContractLogs($aFilters=array()) {
		$bDelete = $this->_getModel('contract_logs_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ContractLog
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractLog($aFilters=array()) {
		$aContractLog = $this->_getModel('contract_logs_m')->getItem($aFilters);
		if (!empty($aContractLog)) {
			return $this->_setupResponse(array($aContractLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ContractLog Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractLogCount($aFilters=array()) {
		$iCount = $this->_getModel('contract_logs_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get ContractLogs
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractLogs($aFilters=array(),$sSort='contract_log_id asc',$iLimit=null) {
		$aContractLogs = $this->_getModel('contract_logs_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aContractLogs);
	}

	/**
	 * Get ContractLogs By TeamMember
	 *
	 * @access public
	 * @param int $iMemberId
	 * @param string $sOrderBy (Optional, default: create_date desc)
	 * @param int $iLimit (Optional, default: 20)
	 * @param int $iOffset (Optional, default: 0)
	 * @return ServiceResponse
	 */
	public function getContractLogsByTeamMember($iMemberId,$iParentId,$sOrderBy='contract_logs.create_date desc',$iLimit=15,$iOffset=0) {
		$aContractLogs = $this->_getModel('contract_logs_m')->getContractLogsByTeamMember($iMemberId,$iParentId,$sOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aContractLogs);
	}

	/**
	 * Scrape usertags from log text
	 *
	 * @access protected
	 * @param string $sText
	 * @return array
	 */
	protected function _findUsers($sText) {
		if (empty($sText) || !is_string($sText)) {
			return array();
		}

		$aUsers = array();

		$sText = str_replace(array("\n","\r\n"),array(' \n ',' \r\n '),$sText);
		$sText = str_replace(array(',',':','/',';','.'),array(' , ',' : ',' / ',' ; ',' . '),$sText);
		$aText = explode(' ',$sText);
		foreach ($aText as $sSection) {
			if (preg_match('/^@\w+$/',$sSection)) {
				$sUser = substr($sSection,1);
				$aUsers[$sUser] = $sUser;
			}
		}

		return $aUsers;
	}

	/**
	 * Update ContractLog
	 *
	 * @access public
	 * @param ContractLogModel $oContractLog
	 * @return ServiceResponse
	 */
	public function updateContractLog(ContractLogModel $oContractLog) {
		if (!$oContractLog->contract_log_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('contract_logs_m')->updateItem($oContractLog->toArray());
		if ($bResponse) {
			$oContractLog->isSaved(true);
			return new ServiceResponse(array($oContractLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
