<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ContractRevisionService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractRevisionModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Generate Revision
	 *
	 * @access public
	 * @param ContractModel $oContract
	 * @return ServiceResponse
	 */
	public function generateRevision($oContract) {
		$aContract = $oContract->toArray();
		//$aContract['revision_date'] = date('Y-m-d H:i:s');
		$aContract['revision_date'] = $oContract->last_updated;

		return $this->addContractRevision(new ContractRevisionModel($aContract));
	}

	/**
	 * Add ContractRevision
	 *
	 * @access public
	 * @param ContractRevisionModel $oContractRevision
	 * @return ServiceResponse
	 */
	public function addContractRevision(ContractRevisionModel $oContractRevision) {
		$iResult = $this->_getModel('contract_revisions_m')->addItem($oContractRevision->toArray());
		if ($iResult) {
			$oContractRevision->contract_revision_id = $iResult;
			$oContractRevision->isSaved(true);
			return new ServiceResponse(array($oContractRevision));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	public function getFileRevisions($aFilters,$iLimit=5) {
		$aContractRevisions = $this->_getModel('contract_revisions_m')->getFileRevisions($aFilters,$iLimit);
		return $this->_setupResponse($aContractRevisions);
	}

	/**
	 * Delete ContractRevisions
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContractRevisions($aFilters=array()) {
		$oContractRevisions = $this->getContractRevisions($aFilters);
		foreach ($oContractRevisions as $oContractRevision) {
			$this->_purgeCachedContractFile($oContractRevision);
		}

		$bDelete = $this->_getModel('contracts_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ContractRevision
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractRevision($aFilters=array()) {
		$aContractRevision = $this->_getModel('contract_revisions_m')->getItem($aFilters);
		if (!empty($aContractRevision)) {
			return $this->_setupResponse(array($aContractRevision));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ContractRevision Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractRevisionCount($aFilters=array()) {
		$iCount = $this->_getModel('contract_revisions_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get ContractRevisions
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractRevisions($aFilters=array(),$sSort='contract_revision_id asc',$iLimit=null) {
		$aContractRevisions = $this->_getModel('contract_revisions_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aContractRevisions);
	}

	/**
	 * Update ContractRevision
	 *
	 * @access public
	 * @param ContractRevisionModel $oContractRevision
	 * @return ServiceResponse
	 */
	public function updateContractRevision(ContractRevisionModel $oContractRevision) {
		if (!$oContractRevision->contract_revision_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('contract_revisions_m')->updateItem($oContractRevision->toArray());
		if ($bResponse) {
			$oContractRevision->isSaved(true);
			return new ServiceResponse(array($oContractRevision));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
