<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class OtherMemberAccountService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'OtherMemberAccountModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add OtherMemberAccount
	 *
	 * @access public
	 * @param OtherMemberAccountModel $oOtherMemberAccount
	 * @return ServiceResponse
	 */
	public function addOtherMemberAccount(OtherMemberAccountModel $oOtherMemberAccount) {
		$iResult = $this->_getModel('other_member_accounts_m')->addItem($oOtherMemberAccount->toArray());
		if ($iResult) {
			$oOtherMemberAccount->other_member_account_id = $iResult;
			$oOtherMemberAccount->isSaved(true);
			return new ServiceResponse(array($oOtherMemberAccount));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete OtherMemberAccount
	 *
	 * @access public
	 * @param OtherMemberAccountModel $oOtherMemberAccount
	 * @return ServiceResponse
	 */
	public function deleteOtherMemberAccount(OtherMemberAccountModel $oOtherMemberAccount) {
		return $this->deleteItems(array('other_member_account_id'=>$oOtherMemberAccount->other_member_account_id));
	}

	/**
	 * Delete OtherMemberAccounts
	 *
	 * @access public
	 * @param $aFilters
	 * @return ServiceResponse
	 */
	public function deleteOtherMemberAccounts($aFilters) {
		return $this->deleteItems($aFilters);
	}

	/**
	 * Get OtherMemberAccount
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getOtherMemberAccount($aFilters=array()) {
		$aOtherMemberAccount = $this->_getModel('other_member_accounts_m')->getItem($aFilters);
		if (!empty($aOtherMemberAccount)) {
			return $this->_setupResponse(array($aOtherMemberAccount));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get OtherMemberAccounts
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getOtherMemberAccounts($aFilters=array(),$sSort='other_member_account_id asc',$iLimit=null,$iOffset=null) {
		$aOtherMemberAccounts = $this->_getModel('other_member_accounts_m')->getItems($aFilters,$sSort,$iLimit,$iOffset);
		return $this->_setupResponse($aOtherMemberAccounts);
	}

	/**
	 * Get OtherMemberAccounts With Member Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getOtherMemberAccountsWithMemberAccountData($aFilters) {
		$aOtherMemberAccounts = $this->_getModel('other_member_accounts_m')->getOtherMemberAccountsWithMemberAccountData($aFilters);
		return $this->_setupResponse($aOtherMemberAccounts);
	}

	/**
	 * Get OtherMemberAccounts With Parent Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getOtherMemberAccountsWithParentAccountData($aFilters) {
		$aOtherMemberAccounts = $this->_getModel('other_member_accounts_m')->getOtherMemberAccountsWithParentAccountData($aFilters);
		return $this->_setupResponse($aOtherMemberAccounts);
	}

	/**
	 * Update OtherMemberAccount
	 *
	 * @access public
	 * @param OtherMemberAccountModel $oOtherMemberAccount
	 * @return ServiceResponse
	 */
	public function updateOtherMemberAccount(OtherMemberAccountModel $oOtherMemberAccount) {
		if (!$oOtherMemberAccount->other_member_account_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('other_member_accounts_m')->updateItem($oOtherMemberAccount->toArray());
		if ($bResponse) {
			$oOtherMemberAccount->isSaved(true);
			return new ServiceResponse(array($oOtherMemberAccount));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
