<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ContractTagService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ContractTagModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add ContractTag
	 *
	 * @access public
	 * @param ContractTagModel $oContractTag
	 * @return ServiceResponse
	 */
	public function addContractTag(ContractTagModel $oContractTag) {
		$iResult = $this->_getModel('contract_tags_m')->addItem($oContractTag->toArray());
		if ($iResult) {
			$oContractTag->contract_tag_id = $iResult;
			$oContractTag->isSaved(true);
			return new ServiceResponse(array($oContractTag));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete ContractTags
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteContractTags($aFilters=array()) {
		$bDelete = $this->_getModel('contract_tags_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ContractTag
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractTag($aFilters=array()) {
		$aContractTag = $this->_getModel('contract_tags_m')->getItem($aFilters);
		if (!empty($aContractTag)) {
			return $this->_setupResponse(array($aContractTag));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ContractTag Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractTagCount($aFilters=array()) {
		$iCount = $this->_getModel('contract_tags_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get ContractTags
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getContractTags($aFilters=array(),$sSort='contract_tag_id asc',$iLimit=null) {
		$aContractTags = $this->_getModel('contract_tags_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aContractTags);
	}

	/**
	 * Update ContractTag
	 *
	 * @access public
	 * @param ContractTagModel $oContractTag
	 * @return ServiceResponse
	 */
	public function updateContractTag(ContractTagModel $oContractTag) {
		if (!$oContractTag->contract_tag_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('contract_tags_m')->updateItem($oContractTag->toArray());
		if ($bResponse) {
			$oContractTag->isSaved(true);
			return new ServiceResponse(array($oContractTag));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
