<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Subscription Log Service Class
 *
 * @access public
 */
class SubscriptionLogService extends Service
{
	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'SubscriptionLogModel';

	///////////////////////////////////////////////////////////////////////////
	/////  Class Methods   ///////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Record Transaction
	 *
	 * @access public
	 * @param integer $iMemberId
	 * @param integer $iSubscriptionId
	 * @param array $aTransaction
	 * @return ServiceResponse
	 */
	public function recordTransaction($iMemberId,$iSubscriptionId,$aTransaction) {
		$oSubscriptionLog = new SubscriptionLogModel(array(
			'member_id'             => $iMemberId
			,'subscription_id'      => $iSubscriptionId
			,'response_code'        => (int) $aTransaction['response_code']
			,'response_reason_code' => (int) $aTransaction['response_reason_code']
			,'create_date'          => date('Y-m-d H:i:s')
			,'message'              => $aTransaction['response_reason_text']
			,'amount'               => $aTransaction['amount']
		));
		if (!empty($aTransaction['trans_id'])) {
			$oSubscriptionLog->trans_id = $aTransaction['trans_id'];
		}
		return $this->addLog($oSubscriptionLog);
	}

	/**
	 * Add Log
	 *
	 * @access public
	 * @param SubscriptionLogModel $oSubscriptionLog
	 * @return ServiceResponse
	 */
	public function addLog(SubscriptionLogModel $oSubscriptionLog) {
		$iSubscriptionLogId = $this->_getModel('subscription_logs_m')->addItem($oSubscriptionLog->toArray());

		if (!empty($iSubscriptionLogId)) {
			$oSubscriptionLog->subscription_log_id = $iSubscriptionLogId;
			$oSubscriptionLog->isSaved(true);
			return new ServiceResponse(array($oSubscriptionLog));
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get Logs
	 *
	 * @access public
	 * @param array $aFilters
	 * @param string $sOrderBy (Optional, Default 'create_date asc')
	 * @param integer $iLimit (Optional)
	 * @param integer $iOffset (Optional)
	 * @return ServiceResponse
	 */
	public function getLogs($aFilters=array(),$sOrderBy='create_date asc',$iLimit=false,$iOffset=false) {
		$aLogs = $this->_getModel('subscription_logs_m')->getItems($aFilters,$sOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aLogs);
	}

	/**
	 * Update Log
	 *
	 * @access public
	 * @param SubscriptionLogModel $oSubscriptionLog
	 * @return ServiceResponse
	 */
	public function updateLog(SubscriptionLogModel $oSubscriptionLog) {
		$bUpdated = $this->_getModel('subscription_logs_m')->updateItem($oSubscriptionLog->toArray());

		if ($bUpdated) {
			$oSubscriptionLog->isSaved(true);
			return new ServiceResponse(array($oSubscriptionLog));
		}

		return $this->_setupErrorResponse();
	}
}
