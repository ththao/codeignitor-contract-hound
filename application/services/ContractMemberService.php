<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ContractMemberService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractMemberModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add ContractMember
	 *
	 * @access public
	 * @param ContractMemberModel $oContractMember
	 * @return ServiceResponse
	 */
	public function addContractMember(ContractMemberModel $oContractMember) {
		$iResult = $this->_getModel('contract_members_m')->addItem($oContractMember->toArray());
		if ($iResult) {
			$oContractMember->contract_member_id = $iResult;
			$oContractMember->isSaved(true);
			return new ServiceResponse(array($oContractMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete ContractMembers
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContractMembers($aFilters=array()) {
		$bDelete = $this->_getModel('contract_members_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ContractMember
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractMember($aFilters=array()) {
		$aContractMember = $this->_getModel('contract_members_m')->getItem($aFilters);
		if (!empty($aContractMember)) {
			return $this->_setupResponse(array($aContractMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ContractMember Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractMemberCount($aFilters=array()) {
		$iCount = $this->_getModel('contract_members_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get ContractMembers
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractMembers($aFilters=array(),$sSort='contract_member_id asc',$iLimit=null) {
		$aContractMembers = $this->_getModel('contract_members_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aContractMembers);
	}

	/**
	 * Update ContractMember
	 *
	 * @access public
	 * @param ContractMemberModel $oContractMember
	 * @return ServiceResponse
	 */
	public function updateContractMember(ContractMemberModel $oContractMember) {
		if (!$oContractMember->contract_member_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('contract_members_m')->updateItem($oContractMember->toArray());
		if ($bResponse) {
			$oContractMember->isSaved(true);
			return new ServiceResponse(array($oContractMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
