<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contract Approval Model
 *
 * @access public
 */
class ContractApprovalModel extends BaseModel
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
		'contract_approval_id'
		,'contract_id'
		,'member_id'
		,'step'
		,'type'
		,'status'
		,'create_date'
		,'due_date'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'step'      => 1
		,'status'   => self::STATUS_PENDING
		,'type'     => self::TYPE_ALL
		,'due_date' => null
	);

	/**
	 * Type Options
	 *
	 * @access public
	 */
	const TYPE_ALL = 0;
	const TYPE_ANY = 1;

	/**
	 * Type Translations
	 *
	 * @access protected
	 */
	protected $aTypes = array(
		self::TYPE_ALL  => 'all'
		,self::TYPE_ANY => 'any'
	);

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const STATUS_PENDING   = 0;
	const STATUS_APPROVED  = 1;
	const STATUS_REJECTED  = 2;
	const STATUS_WAITING   = 3; // Waiting on previous step
	const STATUS_SKIPPED   = 4;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aStatuses = array(
		self::STATUS_PENDING   => 'contract_approval_status_pending'
		,self::STATUS_APPROVED => 'contract_approval_status_approved'
		,self::STATUS_REJECTED => 'contract_approval_status_rejected'
		,self::STATUS_WAITING  => 'contract_approval_status_waiting'
		,self::STATUS_SKIPPED  => 'contract_approval_status_skipped'
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
		return $this->_translateField('status',$this->aStatuses);
	}

	/**
	 * Get Available Statuses
	 *
	 * @access public
	 * @return array
	 */
	public function getAvailableStatuses() {
		return $this->aStatuses;
	}

	/**
	 * Get Readable Types
	 *
	 * @access public
	 * @return string
	 */
	public function getReadableType() {
		return $this->_translateField('type',$this->aTypes);
	}
}
