<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ContractSignatureService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractSignatureModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add ContractSignature
	 *
	 * @access public
	 * @param ContractSignatureModel $oContractSignature
	 * @return ServiceResponse
	 */
	public function addContractSignature(ContractSignatureModel $oContractSignature) {
		$iResult = $this->_getModel('contract_signatures_m')->addItem($oContractSignature->toArray());
		if ($iResult) {
			$oContractSignature->contract_signature_id = $iResult;
			$oContractSignature->isSaved(true);
			return new ServiceResponse(array($oContractSignature));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete ContractSignatures
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContractSignatures($aFilters=array()) {
		$bDelete = $this->_getModel('contract_signatures_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ContractSignature
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractSignature($aFilters=array()) {
		$aContractSignature = $this->_getModel('contract_signatures_m')->getItem($aFilters);
		if (!empty($aContractSignature)) {
			return $this->_setupResponse(array($aContractSignature));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ContractSignature Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractSignatureCount($aFilters=array()) {
		$iCount = $this->_getModel('contract_signatures_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get ContractSignatures
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractSignatures($aFilters=array(),$sSort='member_id asc',$iLimit=null,$iOffset=null) {
		$aContractSignatures = $this->_getModel('contract_signatures_m')->getItems($aFilters,$sSort,$iLimit,$iOffset);
		return $this->_setupResponse($aContractSignatures);
	}

	/**
	 * Get ContractSignatures with Assignees
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractSignaturesWithAssignees($iContractId,$sOrderBy='step asc',$iLimit=20,$iOffset=0) {
		$aContractSignatures = $this->_getModel('contract_signatures_m')->getContractSignaturesWithAssignees($iContractId,$sOrderBy='step asc',$iLimit=20,$iOffset=0);
		return $this->_setupResponse($aContractSignatures);
	}

	/**
	 * Update ContractSignature
	 *
	 * @access public
	 * @param ContractSignatureModel $oContractSignature
	 * @return ServiceResponse
	 */
	public function updateContractSignature(ContractSignatureModel $oContractSignature) {
		if (!$oContractSignature->contract_signature_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('contract_signatures_m')->updateItem($oContractSignature->toArray());
		if ($bResponse) {
			$oContractSignature->isSaved(true);
			return new ServiceResponse(array($oContractSignature));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Update ContractSignatures
	 *
	 * @access public
	 * @param array $aFilters
	 * @param array $aNewValues
	 * @return ServiceResponse
	 */
	public function updateContractSignatures($aFilters,$aNewValues) {
		$bResponse = $this->_getModel('contract_signatures_m')->updateItemsBatch($aFilters,$aNewValues);
		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
