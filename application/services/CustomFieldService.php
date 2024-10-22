<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class CustomFieldService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'CustomFieldModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add CustomField
	 *
	 * @access public
	 * @param CustomFieldModel $oCustomField
	 * @return ServiceResponse
	 */
	public function addCustomField(CustomFieldModel $oCustomField) {
		$iResult = $this->_getModel('custom_fields_m')->addItem($oCustomField->toArray());
		if ($iResult) {
			$oCustomField->custom_field_id = $iResult;
			$oCustomField->isSaved(true);
			return new ServiceResponse(array($oCustomField));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete CustomFields
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteCustomFields($aFilters=array()) {
		$bDelete = $this->_getModel('custom_fields_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get CustomField
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomField($aFilters=array()) {
		$aCustomField = $this->_getModel('custom_fields_m')->getItem($aFilters);
		if (!empty($aCustomField)) {
			return $this->_setupResponse(array($aCustomField));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get CustomField Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFieldCount($aFilters=array()) {
		$iCount = $this->_getModel('custom_fields_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get CustomFields
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFields($aFilters=array(),$sSort='custom_field_id asc',$iLimit=null) {
		$aCustomFields = $this->_getModel('custom_fields_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aCustomFields);
	}

	/**
	 * Update CustomField
	 *
	 * @access public
	 * @param CustomFieldModel $oCustomField
	 * @return ServiceResponse
	 */
	public function updateCustomField(CustomFieldModel $oCustomField) {
		if (!$oCustomField->custom_field_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('custom_fields_m')->updateItem($oCustomField->toArray());
		if ($bResponse) {
			$oCustomField->isSaved(true);
			return new ServiceResponse(array($oCustomField));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
