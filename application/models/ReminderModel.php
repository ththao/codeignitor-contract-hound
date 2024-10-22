<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Reminder Model
 *
 * @access public
 */
class ReminderModel extends BaseModel
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
		'reminder_id'
		,'contract_id'
		,'message'
		,'status'
		,'alert_date'
		,'create_date'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'status' => self::STATUS_ACTIVE
	);

	const STATUS_COMPLETED = 0;
	const STATUS_ACTIVE    = 1;
	const STATUS_FAILED    = 2;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aStatuses = array(
		self::STATUS_COMPLETED => 'Completed'
		,self::STATUS_ACTIVE   => 'Success'
		,self::STATUS_FAILED   => 'Failed'
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
