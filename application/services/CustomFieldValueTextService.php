<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class CustomFieldValueTextService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'CustomFieldValueTextModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add CustomFieldValueText
	 *
	 * @access public
	 * @param CustomFieldValueTextModel $oCustomFieldValueText
	 * @return ServiceResponse
	 */
	public function addCustomFieldValueText(CustomFieldValueTextModel $oCustomFieldValueText) {
		$iResult = $this->_getModel('custom_field_value_text_m')->addItem($oCustomFieldValueText->toArray());
		if ($iResult) {
			$oCustomFieldValueText->custom_field_value_text_id = $iResult;
			$oCustomFieldValueText->isSaved(true);
			return new ServiceResponse(array($oCustomFieldValueText));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete CustomFieldValueTexts
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteCustomFieldValueTexts($aFilters=array()) {
		$bDelete = $this->_getModel('custom_field_value_text_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get CustomFieldValueText
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFieldValueText($aFilters=array()) {
		$aCustomFieldValueText = $this->_getModel('custom_field_value_text_m')->getItem($aFilters);
		if (!empty($aCustomFieldValueText)) {
			return $this->_setupResponse(array($aCustomFieldValueText));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get CustomFieldValueText Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFieldValueTextCount($aFilters=array()) {
		$iCount = $this->_getModel('custom_field_value_text_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get CustomFieldValueTexts
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCustomFieldValueTexts($aFilters=array(),$sSort='custom_field_value_text_id asc',$iLimit=null) {
		$aCustomFieldValueTexts = $this->_getModel('custom_field_value_text_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aCustomFieldValueTexts);
	}

	/**
	 * Update CustomFieldValueText
	 *
	 * @access public
	 * @param CustomFieldValueTextModel $oCustomFieldValueText
	 * @return ServiceResponse
	 */
	public function updateCustomFieldValueText(CustomFieldValueTextModel $oCustomFieldValueText) {
		if (!$oCustomFieldValueText->custom_field_value_text_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('custom_field_value_text_m')->updateItem($oCustomFieldValueText->toArray());
		if ($bResponse) {
			$oCustomFieldValueText->isSaved(true);
			return new ServiceResponse(array($oCustomFieldValueText));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
