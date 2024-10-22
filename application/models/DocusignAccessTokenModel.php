<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Docusign Access Token Model
 *
 * @access public
 */
class DocusignAccessTokenModel extends BaseModel
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
		'docusign_access_token_id'
		,'parent_id'
		,'member_id'
		,'access_token'
		,'token_type'
		,'refresh_token'
		,'name'
		,'email'
		,'account_id'
		,'base_uri'
		,'account_name'
		,'status'
		,'expires_in'
		,'expires_at'
		,'last_updated'
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

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const STATUS_ACTIVE    = 0;
	const STATUS_EXPIRED   = 1;
}
