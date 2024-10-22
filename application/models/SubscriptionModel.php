<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Subscription Model
 *
 * @access public
 */
class SubscriptionModel extends BaseModel
{
	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Fields for model
	 *
	 * @access protected
	 */
	protected $aFields = array(
		'subscription_id'
		,'member_id'
		,'create_date'
		,'plan_id'
		,'contract_limit'
		,'status'
		,'approvals'
		,'price'
		,'start_date'
		,'last_checked'
		,'last_changed'
		,'cancel_date'
		,'expire_date'
		,'next_billing_date'
		,'stripe_id'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'status'     => self::StatusFree
		,'approvals' => self::APPROVALS_DISABLED
	);

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const StatusTrial      = 0; // Trial
	const StatusActive     = 1; // Active
	const StatusExpired    = 2; // Expired
	const StatusCancelled  = 3; // They cancelled
	const StatusSuspended  = 4; // On hold
	const StatusTerminated = 5; // We Killed it
	const StatusFree       = 6; // Free

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aStatuses = array(
		self::StatusActive      => 'subscription_status_active'
		,self::StatusExpired    => 'subscription_status_expired'
		,self::StatusCancelled  => 'subscription_status_cancelled'
		,self::StatusSuspended  => 'subscription_status_suspended'
		,self::StatusTerminated => 'subscription_status_terminated'
		,self::StatusTrial      => 'subscription_status_trial'
	);

	/**
	 * Approvals
	 *
	 * @access public
	 */
	const APPROVALS_DISABLED = 0;
	const APPROVALS_ENABLED  = 1;

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Get Status Options
	 *
	 * @access public
	 * @return array
	 */
	public function getStatusOptions() {
		return $this->aStatuses;
	}

	/**
	 * Get Type Options
	 *
	 * @access public
	 * @return array
	 */
	public function getTypeOptions() {
		return $this->aTypes;
	}

	/**
	 * Set Status
	 *
	 * @access public
	 * @param integer $iStatus
	 * @return boolean
	 */
	public function setStatus($iStatus) {
		if (is_numeric($iStatus) && !empty($this->aStatuses[$iStatus])) {
			return $this->aData['status'] = $iStatus;
		}

		return false;
	}

	/**
	 * Is Active
	 *
	 * @access public
	 * @return boolean
	 */
	public function isActive() {
		if (!isset($this->aData['status']) || empty($this->aData['subscription_id'])) {
			return false;
		}

		$bActive = false;
		switch ($this->aData['status']) {
			case self::StatusFree:
			case self::StatusActive:
			case self::StatusTrial:
				$bActive = true;
				break;
			case self::StatusSuspended:
			case self::StatusCancelled:
			case self::StatusExpired:
				if (isset($this->aData['expire_date'])) {
					$iExpireDate = strtotime($this->aData['expire_date']);
					if ($iExpireDate >= time()) {
						$bActive = true;
					}
				}
				break;
		}

		return $bActive;
	}

	/**
	 * Was checked in last 24 hours
	 *
	 * @access public
	 * @return boolean
	 */
	public function isChecked() {
		if ($this->isActive()) {
			if (empty($this->aData['last_checked']) ||
				strtotime($this->aData['last_checked']) < strtotime('-24 hours'))
			{
				return false;
			} else {
				return true;
			}
		}

		return false;
	}

	/**
	 * Is Expired
	 *
	 * @access public
	 * @return boolean
	 */
	public function isExpired() {
		if (!isset($this->aData['expire_date']) || empty($this->aData['subscription_id'])) {
			return false;
		}

		$bExpired = false;
		if (isset($this->aData['expire_date'])) {
			$iExpireDate = strtotime($this->aData['expire_date']);
			if ($iExpireDate <= time()) {
				$bExpired = true;
			}
		}

		return $bExpired;
	}

	/**
	 * Get Readable Statuses
	 *
	 * @access public
	 * @return string
	 */
	public function getReadableStatus() {
		return $this->_getReadable('status',$this->aStatuses);
	}
	
	/**
	 * Get Readable Statuses
	 *
	 * @access public
	 * @return string
	 */
	public function getTranslatedStatus() {
	    $CI = &get_instance();
	    $CI->lang->load('subscriptions_lang', 'english');
	    
	    $statuses = [];
	    foreach ($this->aStatuses as $s => $v) {
	        $statuses[$s] = $CI->lang->line($v);
	    }
	    return $this->_getReadable('status', $statuses);
	}
}
