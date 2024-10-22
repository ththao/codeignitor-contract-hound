<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/services/EncryptedFileService.php');
class CountryService extends EncryptedFileService {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'CountryModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	/**
	 * Get Contract
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCountries($aFilters=array(), $aOrders=array()) {
	    $aCountry = $this->_getModel('country_m')->getItems($aFilters, $aOrders);
		if (!empty($aCountry)) {
			return $this->_setupResponse($aCountry);
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Contract
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getCountry($aFilters=array()) {
		$aCountry = $this->_getModel('country_m')->getItem($aFilters);
		if (!empty($aCountry)) {
		    return $this->_setupResponse(array($aCountry));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
