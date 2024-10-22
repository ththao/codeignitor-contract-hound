<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/models/BillingLogModel.php');
/**
 * Billing Log Service Class
 *
 * @access public
 */
class BillingLogService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'BillingLogModel';

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
	 * Add Billing Log
	 *
	 * @access public
	 * @param BillingLogModel $oBillingLog
	 * @return ServiceResponse
	 */
	public function addBillingLog(BillingLogModel $oBillingLog) {
		$oBillingLog->create_date = date('Y-m-d H:i:s');
		$iResult = $this->_getModel('billing_log_m')->addItem($oBillingLog->toArray());

		if ($iResult) {
			$oBillingLog->billing_log_id = $iResult;
			$oBillingLog->isSaved(true);
			return new ServiceResponse(array($oBillingLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Billing Log
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteBillingLog($aFilters) {
		$bResponse = $this->_getModel('billing_log_m')->deleteItems($aFilters);

		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Billing Log
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getBillingLog($aFilters=array()) {
		$aBillingLog = $this->_getModel('billing_log_m')->getItem($aFilters);

		if (!empty($aBillingLog)) {
			return $this->_setupResponse(array($aBillingLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Billing Log
	 *
	 * @access public
	 * @param array $aFilters
	 * @param string $sOrderBy (Optional, 'subscription_id asc')
	 * @param integer $iLimit (Optional)
	 * @param integer $iOffset (Optional)
	 * @return ServiceResponse
	 */
	public function getBillingLogs($aFilters=array(),$sOrderBy='create_date desc',$iLimit=0,$iOffset=0) {
		$aBillingLogs = $this->_getModel('billing_log_m')->getItems($aFilters,$sOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aBillingLogs);
	}

	/**
	 * Get Last Billing Log
	 *
	 * @access public
	 * @param integer $iMemberId
	 * @return ServiceResponse
	 */
	public function getLastBillingLog($iMemberId) {
		if (empty($iMemberId)) {
			return new BillingLogModel();
		}

		$aBillingLog = $this->_getModel('billing_log_m')->getItems(array(
			'member_id' => $iMemberId
		),'create_date desc',1);

		if (!empty($aBillingLog)) {
			return $this->_setupResponse($aBillingLog);
		}

		return new ServiceResponse(array(new BillingLogModel()));
	}

	/**
	 * Update Billing Log
	 *
	 * @access public
	 * @param BillingLogModel $oBillingLog
	 * @return ServiceResponse
	 */
	public function updateBillingLog(BillingLogModel $oBillingLog) {
		$bResponse = $this->_getModel('billing_log_m')->updateItem($oBillingLog->toArray());
		if ($bResponse) {
			$oBillingLog->isSaved(true);
			return new ServiceResponse(array($oBillingLog));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
