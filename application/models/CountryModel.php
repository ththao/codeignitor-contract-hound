<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Coupon Model
 *
 * @access public
 */
class CountryModel extends BaseModel
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
		'id'
		,'locale'
		,'country'
		,'status'
	    ,'currency'
		,'country_code'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
}
