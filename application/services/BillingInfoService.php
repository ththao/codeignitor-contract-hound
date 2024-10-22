<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/models/BillingInfoModel.php');
/**
 * Billing Info Service Class
 *
 * @access public
 */
class BillingInfoService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'BillingInfoModel';

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
	 * Add Billing Info
	 *
	 * @access public
	 * @param BillingInfoModel $oBillingInfo
	 * @return ServiceResponse
	 */
	public function addBillingInfo(BillingInfoModel $oBillingInfo) {
		$oBillingInfo->create_date = date('Y-m-d H:i:s');
		$iResult = $this->_getModel('billing_info_m')->addItem($oBillingInfo->toArray());

		if ($iResult) {
			$oBillingInfo->billing_info_id = $iResult;
			$oBillingInfo->isSaved(true);
			return new ServiceResponse(array($oBillingInfo));
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
	public function deleteBillingInfo($aFilters) {
		$bResponse = $this->_getModel('billing_info_m')->deleteItems($aFilters);

		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Billing Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getBillingInfo($aFilters=array()) {
		$aBillingInfo = $this->_getModel('billing_info_m')->getItem($aFilters);

		if (!empty($aBillingInfo)) {
			return $this->_setupResponse(array($aBillingInfo));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Billing Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getBillingInfos($aFilters=array()) {
		$aBillingInfos = $this->_getModel('billing_info_m')->getItems($aFilters);
		return $this->_setupResponse($aBillingInfos);
	}

	/**
	 * Get Last Billing Info
	 *
	 * @access public
	 * @param integer $iMemberId
	 * @return ServiceResponse
	 */
	public function getLastBillingInfo($iMemberId) {
		if (empty($iMemberId)) {
			return new BillingInfoModel();
		}

		$aBillingInfo = $this->_getModel('billing_info_m')->getItems(array(
			'member_id' => $iMemberId
		),'create_date desc',1);

		if (!empty($aBillingInfo)) {
			return $this->_setupResponse($aBillingInfo);
		}

		return new ServiceResponse(array(new BillingInfoModel()));
	}

	/**
	 * Update Billing Info
	 *
	 * @access public
	 * @param BillingInfoModel $oBillingInfo
	 * @return ServiceResponse
	 */
	public function updateBillingInfo(BillingInfoModel $oBillingInfo) {
		$bResponse = $this->_getModel('billing_info_m')->updateItem($oBillingInfo->toArray());
		if ($bResponse) {
			$oBillingInfo->isSaved(true);
			return new ServiceResponse(array($oBillingInfo));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
