<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class CustomFieldValueCheckboxService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'CustomFieldValueCheckboxModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add CustomFieldValueCheckbox
	 *
	 * @access public
	 * @param CustomFieldValueCheckboxModel $oCustomFieldValueCheckbox
	 * @return ServiceResponse
	 */
	public function addCustomFieldValueCheckbox(CustomFieldValueCheckboxModel $oCustomFieldValueCheckbox) {
		$iResult = $this->_getModel('custom_field_value_checkbox_m')->addItem($oCustomFieldValueCheckbox->toArray());
		if ($iResult) {
			$oCustomFieldValueCheckbox->custom_field_value_checkbox_id = $iResult;
			$oCustomFieldValueCheckbox->isSaved(true);
			return new ServiceResponse(array($oCustomFieldValueCheckbox));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete CustomFieldValueCheckboxes
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteCustomFieldValueCheckboxes($aFilters=array()) {
		$bDelete = $this->_getModel('custom_field_value_checkbox_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get CustomFieldValueCheckbox
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFieldValueCheckbox($aFilters=array()) {
		$aCustomFieldValueCheckbox = $this->_getModel('custom_field_value_checkbox_m')->getItem($aFilters);
		if (!empty($aCustomFieldValueCheckbox)) {
			return $this->_setupResponse(array($aCustomFieldValueCheckbox));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get CustomFieldValueCheckbox Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFieldValueCheckboxCount($aFilters=array()) {
		$iCount = $this->_getModel('custom_field_value_checkbox_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get CustomFieldValueCheckboxes
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFieldValueCheckboxes($aFilters=array(),$sSort='custom_field_value_checkbox_id asc',$iLimit=null) {
		$aCustomFieldValueCheckboxs = $this->_getModel('custom_field_value_checkbox_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aCustomFieldValueCheckboxs);
	}

	/**
	 * Update CustomFieldValueCheckbox
	 *
	 * @access public
	 * @param CustomFieldValueCheckboxModel $oCustomFieldValueCheckbox
	 * @return ServiceResponse
	 */
	public function updateCustomFieldValueCheckbox(CustomFieldValueCheckboxModel $oCustomFieldValueCheckbox) {
		if (!$oCustomFieldValueCheckbox->custom_field_value_checkbox_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('custom_field_value_checkbox_m')->updateItem($oCustomFieldValueCheckbox->toArray());
		if ($bResponse) {
			$oCustomFieldValueCheckbox->isSaved(true);
			return new ServiceResponse(array($oCustomFieldValueCheckbox));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
