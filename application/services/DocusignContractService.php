<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Docusign Contract Service
 *
 * @access public
 */
class DocusignContractService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'DocusignContractModel';

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
	 * Add DocusignContract
	 *
	 * @access public
	 * @param DocusignContractModel $oDocusignContract
	 * @return ServiceResponse
	 */
	public function addDocusignContract(DocusignContractModel $oDocusignContract) {
		$oDocusignContract->create_date = date('Y-m-d H:i:s');

		$iResult = $this->_getModel('docusign_contracts_m')->addItem($oDocusignContract->toArray());

		if ($iResult) {
			$oDocusignContract->docusign_contract_id = $iResult;
			$oDocusignContract->isSaved(true);
			return new ServiceResponse(array($oDocusignContract));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Billing Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getDocusignContractsForAccount($iParentId,$iStatus) {
		$aDocusignContracts = $this->_getModel('docusign_contracts_m')->getDocusignContractsForAccount($iParentId,$iStatus);
		return $this->_setupResponse($aDocusignContracts);
	}

	/**
	 * Delete Billing Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteDocusignContract($aFilters) {
		$bResponse = $this->_getModel('docusign_contracts_m')->deleteItems($aFilters);

		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get DocusignContract
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getDocusignContract($aFilters=array()) {
		$aDocusignContract = $this->_getModel('docusign_contracts_m')->getItem($aFilters);

		if (!empty($aDocusignContract)) {
			return $this->_setupResponse(array($aDocusignContract));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get DocusignContracts
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getDocusignContracts($aFilters=array(),$mOrderBy='last_checked asc',$iLimit=50,$iOffset=0) {
		$aDocusignContracts = $this->_getModel('docusign_contracts_m')->getItems($aFilters,$mOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aDocusignContracts);
	}

	/**
	 * Update DocusignContract
	 *
	 * @access public
	 * @param DocusignContract $oDocusignContract
	 * @return ServiceResponse
	 */
	public function updateDocusignContract(DocusignContractModel $oDocusignContract) {
		$bResponse = $this->_getModel('docusign_contracts_m')->updateItem($oDocusignContract->toArray());
		if ($bResponse) {
			$oDocusignContract->isSaved(true);
			return new ServiceResponse(array($oDocusignContract));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
