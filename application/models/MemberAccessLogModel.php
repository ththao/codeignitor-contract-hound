<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Member Access Log Model
 *
 * @access public
 */
class MemberAccessLogModel extends BaseModel
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
		'member_access_log_id'
		,'member_id'
		,'action_type'
		,'create_date'
	);

	const ACTION_TYPE_LOGIN_SUCCESS = 0;
	const ACTION_TYPE_LOGIN_FAIL = 1;
	const ACTION_TYPE_PASSWORD_RESET  = 2;

	/**
	 * Action Types Translations
	 *
	 * @access protected
	 */
	protected $aActionTypes = array(
		self::ACTION_TYPE_LOGIN_SUCCESS  => 'Login Successful'
		,self::ACTION_TYPE_LOGIN_FAIL => 'Login Failed'
		,self::ACTION_TYPE_PASSWORD_RESET  => 'Password Reset'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Get Readable Action Type
	 *
	 * @access public
	 * @return string
	 */
	public function getReadableActionType() {
		return $this->_getReadable('action_type',$this->aActionTypes);
	}

}
