<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/services/EncryptedFileService.php');
class ContractService extends EncryptedFileService {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add Contract
	 *
	 * @access public
	 * @param ContractModel $oContract
	 * @param string $sFileLocation
	 * @return ServiceResponse
	 */
	public function addContract(ContractModel $oContract,$sFileLocation) {
		$iResult = $this->_getModel('contracts_m')->addItem($oContract->toArray());
		if ($iResult) {
			$oContract->contract_id = $iResult;
			$oContract->isSaved(true);
			$this->storeFile($oContract,$sFileLocation);
			return new ServiceResponse(array($oContract));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Contracts
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContracts($aFilters=array()) {
		// need to remove contracts from db
		$aContractIds = array();
		$oContracts = $this->getContracts($aFilters);
		foreach ($oContracts as $oContract) {
			$aContractIds[] = $oContract->contract_id;
			$this->_purgeCachedContractFile($oContract);
		}

		$bDelete = $this->_getModel('contracts_m')->deleteItems($aFilters);

		if ($bDelete) {
			Service::load('contractmember')->deleteContractMembers(array('contract_id'=>$aContractIds));
			Service::load('contractlog')->deleteContractLogs(array('contract_id'=>$aContractIds));
			Service::load('contracttag')->deleteContractTags(array('contract_id'=>$aContractIds));
			Service::load('contractrevision')->deleteContractRevisions(array('contract_id'=>$aContractIds));

			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	public function getBoardContractCounts($aBoardIds) {
		if (empty($aBoardIds)) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}
		$aCounts = $this->_getModel('contracts_m')->getBoardContractCounts($aBoardIds);
		return new ServiceResponse($aCounts);
	}

	public function getBoardContractCountsV2($iMemberId,$iParentId,$aBoardIds) {
		if (empty($aBoardIds)) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}
		$aCounts = $this->_getModel('contracts_m')->getBoardContractCountsV2($iMemberId,$iParentId,$aBoardIds);
		return new ServiceResponse($aCounts);
	}

	/**
	 * Get Contract
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContract($aFilters=array()) {
		$aContract = $this->_getModel('contracts_m')->getItem($aFilters);
		if (!empty($aContract)) {
			return $this->_setupResponse(array($aContract));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Contract Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractCount($aFilters=array()) {
		$iCount = $this->_getModel('contracts_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get Contracts
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContracts($aFilters=array(),$sSort='contract_id asc',$iLimit=null,$iOffset=0) {
		$aContracts = $this->_getModel('contracts_m')->getItems($aFilters,$sSort,$iLimit,$iOffset);
		return $this->_setupResponse($aContracts);
	}
	
	/**
	 * Get Contracts to Export
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function exportContracts($aFilters=array(),$sSort='contract_id asc',$iLimit=null,$iOffset=0) {
	    $aContracts = $this->_getModel('contracts_m')->exportContracts($aFilters,$sSort,$iLimit,$iOffset);
	    return $this->_setupResponse($aContracts);
	}
	
	public function getContractCountsForMembers($aMemberIds) {
		if (empty($aMemberIds) || !is_array($aMemberIds)) {
			return new ServiceResponse();
		}

		$aContractCounts = $this->_getModel('contracts_m')->getContractCountsByMember($aMemberIds);
		if (empty($aContractCounts)) {
			return new ServiceResponse();
		}

		$aContractCountsByMember = array();
		foreach ($aContractCounts as $aContractCount) {
			$aContractCountsByMember[$aContractCount['owner_id']] = $aContractCount['contract_count'];
		}

		return new ServiceResponse($aContractCountsByMember);
	}

	/**
	 * Get Contracts
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractsByTeamMember($iMemberId,$iParentId,$sOrderBy='cs.create_date desc',$iLimit=20,$iOffset=0) {
		$aContracts = $this->_getModel('contracts_m')->getContractsByTeamMember($iMemberId,$iParentId,$sOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aContracts);
	}

	public function searchContracts($iMemberId,$iParentId,$sSearchTerm,$iBoardId=null,$sOrderBy='cs.create_date desc',$iLimit=20,$iOffset=0) {
		$aContracts = $this->_getModel('contracts_m')->searchContracts($iMemberId,$iParentId,$sSearchTerm,$iBoardId,$sOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aContracts);
	}

	/**
	 * Update Contract
	 *
	 * @access public
	 * @param ContractModel $oContract
	 * @param string $sFileLocation (Optional)
	 * @return ServiceResponse
	 */
	public function updateContract(ContractModel $oContract,$sFileLocation='') {
		if (!$oContract->contract_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		if (!empty($sFileLocation)) {
			$oContract->regenerateFileHash();
		}

		$bResponse = $this->_getModel('contracts_m')->updateItem($oContract->toArray());
		if ($bResponse) {
			$oContract->isSaved(true);
			if (!empty($sFileLocation)) {
				$this->storeFile($oContract,$sFileLocation);
			}
			return new ServiceResponse(array($oContract));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	public function updateFile(ContractModel $oContract) {
		return $this->updateContract($oContract);
	}

	/**
	 * Update Contracts
	 *
	 * @access public
	 * @param array $aFilters
	 * @param array $aNewValues
	 * @return ServiceResponse
	 */
	public function updateContracts($aFilters,$aNewValues) {
		$bResponse = $this->_getModel('contracts_m')->updateItemsBatch($aFilters,$aNewValues);
		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
