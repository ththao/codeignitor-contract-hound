<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/services/EncryptedFileService.php');
class ContractSupportDocService extends EncryptedFileService {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractSupportDocModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add ContractSupportDoc
	 *
	 * @access public
	 * @param ContractSupportDocModel $oContractSupportDoc
	 * @param string $sFileLocation
	 * @return ServiceResponse
	 */
	public function addContractSupportDoc($oContractSupportDoc,$sFileLocation) {
		$iResult = $this->_getModel('contract_support_docs_m')->addItem($oContractSupportDoc->toArray());
		if ($iResult) {
			$oContractSupportDoc->contract_support_doc_id = $iResult;
			$oContractSupportDoc->isSaved(true);
			$this->storeFile($oContractSupportDoc,$sFileLocation);
			return new ServiceResponse(array($oContractSupportDoc));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete ContractSupportDocs
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContractSupportDocs($aFilters=array()) {
		// need to remove contracts from db
		$aContractIds = array();
		$oContracts = $this->getContractSupportDocs($aFilters);
		foreach ($oContracts as $oContract) {
			$aContractSupportDocIds[] = $oContract->contract_support_doc_id;
			$this->_purgeCachedContractFile($oContract);
		}

		$bDelete = $this->_getModel('contract_support_docs_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ContractSupportDoc
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractSupportDoc($aFilters=array()) {
		$aContractSupportDoc = $this->_getModel('contract_support_docs_m')->getItem($aFilters);
		if (!empty($aContract)) {
			return $this->_setupResponse(array($aContractSupportDoc));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ContractSupportDocs
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractSupportDocs($aFilters=array(),$sSort='contract_support_doc_id asc',$iLimit=null,$iOffset=0) {
		$aContractSupportDocs = $this->_getModel('contract_support_docs_m')->getItems($aFilters,$sSort,$iLimit,$iOffset);
		return $this->_setupResponse($aContractSupportDocs);
	}

	/**
	 * Update ContractSupportDoc
	 *
	 * @access public
	 * @param ContractSupportDocModel $oContractSupportDoc
	 * @param string $sFileLocation (Optional)
	 * @return ServiceResponse
	 */
	public function updateContractSupportDoc(ContractSupportDocModel $oContractSupportDoc,$sFileLocation='') {
		if (!$oContractSupportDoc->contract_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		if (!empty($sFileLocation)) {
			$oContractSupportDoc->regenerateFileHash();
		}

		$bResponse = $this->_getModel('contract_support_docs_m')->updateItem($oContractSupportDoc->toArray());
		if ($bResponse) {
			$oContractSupportDoc->isSaved(true);
			if (!empty($sFileLocation)) {
				$this->storeFile($oContractSupportDoc,$sFileLocation);
			}
			return new ServiceResponse(array($oContractSupportDoc));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	public function updateFile(ContractSupportDocModel $oContractSupportDoc) {
		return $this->updateContractSupportDoc($oContractSupportDoc);
	}

}
