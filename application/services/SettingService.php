<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class SettingService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'SettingModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add Setting
	 *
	 * @access public
	 * @param SettingModel $oSetting
	 * @return ServiceResponse
	 */
	public function addSetting(SettingModel $oSetting) {
		$iResult = $this->_getModel('settings_m')->addItem($oSetting->toArray());
		if ($iResult) {
			$oSetting->setting_id = $iResult;
			$oSetting->isSaved(true);
			return new ServiceResponse(array($oSetting));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Settings
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteSettings($aFilters=array()) {
		$bDelete = $this->_getModel('settings_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get Setting
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getSetting($aFilters=array()) {
		$aSetting = $this->_getModel('settings_m')->getItem($aFilters);
		if (!empty($aSetting)) {
			return $this->_setupResponse(array($aSetting));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Setting Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getSettingCount($aFilters=array()) {
		$iCount = $this->_getModel('settings_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get Settings
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getSettings($aFilters=array(),$sSort='setting_id asc',$iLimit=null) {
		$aSettings = $this->_getModel('settings_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aSettings);
	}

	/**
	 * Update Setting
	 *
	 * @access public
	 * @param SettingModel $oSetting
	 * @return ServiceResponse
	 */
	public function updateSetting(SettingModel $oSetting) {
		if (!$oSetting->setting_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('settings_m')->updateItem($oSetting->toArray());
		if ($bResponse) {
			$oSetting->isSaved(true);
			return new ServiceResponse(array($oSetting));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
