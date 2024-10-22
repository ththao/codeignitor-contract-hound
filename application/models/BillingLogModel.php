<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Billing Log Model
 *
 * @access public
 */
class BillingLogModel extends BaseModel
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
		'billing_log_id'
		,'member_id'
		,'subscription_id'
		,'plan_id'
		,'contract_limit'
		,'create_date'
		,'amount'
		,'status'
	);

	const STATUS_PENDING = 0;
	const STATUS_SUCCESS = 1;
	const STATUS_FAILED  = 2;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aStatuses = array(
		self::STATUS_PENDING  => 'Pending'
		,self::STATUS_SUCCESS => 'Success'
		,self::STATUS_FAILED  => 'Failed'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Get Readable Statuses
	 *
	 * @access public
	 * @return string
	 */
	public function getReadableStatus() {
		return $this->_getReadable('status',$this->aStatuses);
	}

}
