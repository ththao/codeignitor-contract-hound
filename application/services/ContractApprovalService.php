<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ContractApprovalService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractApprovalModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add ContractApproval
	 *
	 * @access public
	 * @param ContractApprovalModel $oContractApproval
	 * @return ServiceResponse
	 */
	public function addContractApproval(ContractApprovalModel $oContractApproval) {
		$iResult = $this->_getModel('contract_approvals_m')->addItem($oContractApproval->toArray());
		if ($iResult) {
			$oContractApproval->contract_approval_id = $iResult;
			$oContractApproval->isSaved(true);
			return new ServiceResponse(array($oContractApproval));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete ContractApprovals
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContractApprovals($aFilters=array()) {
		$bDelete = $this->_getModel('contract_approvals_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ContractApproval
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractApproval($aFilters=array()) {
		$aContractApproval = $this->_getModel('contract_approvals_m')->getItem($aFilters);
		if (!empty($aContractApproval)) {
			return $this->_setupResponse(array($aContractApproval));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ContractApproval Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractApprovalCount($aFilters=array()) {
		$iCount = $this->_getModel('contract_approvals_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get ContractApprovals
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractApprovals($aFilters=array(),$sSort='step asc, member_id asc',$iLimit=null,$iOffset=null) {
		$aContractApprovals = $this->_getModel('contract_approvals_m')->getItems($aFilters,$sSort,$iLimit,$iOffset);
		return $this->_setupResponse($aContractApprovals);
	}

	/**
	 * Get ContractApprovals with Assignees
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractApprovalsWithAssignees($iContractId,$sOrderBy='step asc',$iLimit=20,$iOffset=0) {
		$aContractApprovals = $this->_getModel('contract_approvals_m')->getContractApprovalsWithAssignees($iContractId,$sOrderBy='step asc',$iLimit=20,$iOffset=0);
		return $this->_setupResponse($aContractApprovals);
	}

	/**
	 * Update ContractApproval
	 *
	 * @access public
	 * @param ContractApprovalModel $oContractApproval
	 * @return ServiceResponse
	 */
	public function updateContractApproval(ContractApprovalModel $oContractApproval) {
		if (!$oContractApproval->contract_approval_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('contract_approvals_m')->updateItem($oContractApproval->toArray());
		if ($bResponse) {
			$oContractApproval->isSaved(true);
			return new ServiceResponse(array($oContractApproval));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Update ContractApprovals
	 *
	 * @access public
	 * @param array $aFilters
	 * @param array $aNewValues
	 * @return ServiceResponse
	 */
	public function updateContractApprovals($aFilters,$aNewValues) {
		$bResponse = $this->_getModel('contract_approvals_m')->updateItemsBatch($aFilters,$aNewValues);
		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
