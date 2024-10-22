<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contract Signature Model
 *
 * @access public
 */
class ContractSignatureModel extends BaseModel
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
		'contract_signature_id'
		,'contract_id'
		,'member_id'
		,'status'
		,'create_date'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'status'   => self::STATUS_WAITING
	);

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const STATUS_WAITING  = 0; // Waiting on approvals
	const STATUS_PENDING  = 1;
	const STATUS_SIGNED   = 2;
	const STATUS_REJECTED = 3;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aStatuses = array(
		self::STATUS_WAITING   => 'contract_signature_status_waiting'
		,self::STATUS_PENDING  => 'contract_signature_status_pending'
		,self::STATUS_SIGNED   => 'contract_signature_status_signed'
		,self::STATUS_REJECTED => 'contract_signature_status_rejected'
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
}
