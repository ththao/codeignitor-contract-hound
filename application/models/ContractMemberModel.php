<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contract Member Model
 *
 * @access public
 */
class ContractMemberModel extends BaseModel
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
		'contract_member_id'
		,'contract_id'
		,'member_id'
		,'level'
		,'muted'
		,'create_date'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'level'  => self::LEVEL_VIEW_ONLY
		,'muted' => self::MUTED_NO
	);

	/**
	 * Muted Options
	 */
	const MUTED_NO  = 0;
	const MUTED_YES = 1;

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const LEVEL_EDITOR    = 0;
	const LEVEL_VIEW_ONLY = 1;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aLevels = array(
		self::LEVEL_EDITOR     => 'Editor'
		,self::LEVEL_VIEW_ONLY => 'View Only'
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
	public function getReadableLevel() {
		return $this->_translateField('level',$this->aLevels);
	}

	/**
	 * Get Available Statuses
	 *
	 * @access public
	 * @return array
	 */
	public function getAvailableLevels() {
		return $this->aLevels;
	}
}
