<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contract Model
 *
 * @access public
 */
class ContractModel extends EncryptedFileModel
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
		'contract_id'
		,'owner_id'
		,'parent_id'
		,'board_id'
		,'name'
		,'company'
		,'start_date'
		,'end_date'
		,'valued'
		,'type'
		,'status'
		,'enct'
		,'file_name'
		,'file_hash'
		,'ivlen'
		,'iv'
		,'last_updated'
		,'create_date'
	    ,'docusign_error'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'company' => ''
		,'status' => self::STATUS_ACTIVE
		,'type'   => self::TYPE_BUY_SIDE
		,'enct'   => 0
	);

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const STATUS_ACTIVE    = 0;
	const STATUS_EXPIRED   = 1;
	const STATUS_ARCHIVED  = 2;
	const STATUS_DELETED   = 3;
	
	const DOCUSIGN_ERROR_CREATE = 1;
	const DOCUSIGN_ERROR_GET = 2;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aStatuses = array(
		self::STATUS_ACTIVE   => 'active'
		,self::STATUS_EXPIRED => 'expired'
		,self::STATUS_DELETED => 'deleted'
	);

	const TYPE_SELL_SIDE = 0;
	const TYPE_BUY_SIDE  = 1;

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

	public function getKeyId() {
		return $this->contract_id;
	}
}
