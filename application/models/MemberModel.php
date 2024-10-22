<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Members Model
 *
 * @access public
 */
class MemberModel extends BaseModel
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
		 'member_id'
		,'email'
		,'password'
		,'first_name'
		,'last_name'
		,'company'
		,'role'
		,'avatar'
		,'notification_frequency'
		,'notify_contract_changes'
		,'notify_add_comment'
		,'notify_board_change'
		,'notify_contract_status_change'
		,'notify_contract_ending'
		,'status'
		,'count_failed_login_attempts'
		,'create_date'
		,'parent_id'
		,'stripe_id'
		,'country_id'
	    ,'currency'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'notification_frequency'         => self::NOTIFICATION_FREQUENCY_DAILY
		,'notify_contract_changes'       => self::NOTIFY_YES
		,'notify_add_comment'            => self::NOTIFY_YES
		,'notify_board_change'           => self::NOTIFY_YES
		,'notify_contract_status_change' => self::NOTIFY_YES
		,'notify_contract_ending'        => self::NOTIFY_YES
		,'status'                        => self::StatusPending
		,'first_name'                    => ''
		,'last_name'                     => ''
		,'company'                       => ''
		,'role'                          => ''
		,'avatar'                        => ''
		,'count_failed_login_attempts'   => 0
		//,'country_id'   => 132
	);

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const StatusPending   = 0;
	const StatusActive    = 1;
	const StatusExpired   = 2;
	const StatusSuspended = 3;
	const StatusDeleted   = 4;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aStatuses = array(
		 self::StatusPending   => 'pending'
		,self::StatusActive    => 'active'
		,self::StatusExpired   => 'expired'
		,self::StatusSuspended => 'suspended'
		,self::StatusDeleted   => 'deleted'
	);

	/**
	 * Notification Options
	 */
	const NOTIFICATION_FREQUENCY_AS_HAPPENS = 0;
	const NOTIFICATION_FREQUENCY_DAILY      = 1;
	const NOTIFICATION_FREQUENCY_NEVER      = 2;

	/**
	 * Notify Options
	 */
	const NOTIFY_NO  = 0;
	const NOTIFY_YES = 1;

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function getName() {
		$sName = $this->aData['email'];
		if (empty($this->aData['first_name']) && empty($this->aData['last_name'])) {
			return $sName;
		}

		if (!empty($this->aData['first_name'])) {
			$sName = $this->aData['first_name'];
		}

		if (!empty($this->aData['first_name']) && !empty($this->aData['last_name'])) {
			$sName .= ' ';
		}

		if (!empty($this->aData['last_name'])) {
			$sName .= $this->aData['last_name'];
		}

		return $sName;
	}

	/**
	 * Make sure the email is lowercase
	 *
	 * @access public
	 * @param string $sEmail
	 * @return boolean
	 */
	public function setEmail($sEmail) {
		return $this->aData['email'] = strtolower($sEmail);
	}

	/**
	 * Get Password Reset Token
	 *
	 * @access public
	 * @return string
	 */
	public function getEmailConfirmationToken() {
		if (!empty($this->aData['member_id']) &&
			!empty($this->aData['email']))
		{
			return md5(md5($this->aData['email'].'^FP2C*Ml8,2.').'2R#ukX"W1l,'.$this->aData['member_id']);
		}

		return null;
	}

	/**
	 * Get Password Reset Token
	 *
	 * @access public
	 * @return string
	 */
	public function getPasswordResetToken() {
		if (!empty($this->aData['member_id']) &&
			!empty($this->aData['email']) &&
			!empty($this->aData['password']))
		{
			return md5($this->aData['email'].'B@U^FP2C*MlDi6ilnzAJLMyOqndgPV21znBN6b7FKC48,2.hT#5U"2R#ukXW1'.$this->aData['password'].'2."T#5U'.$this->aData['member_id']);
		}

		return null;
	}

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
