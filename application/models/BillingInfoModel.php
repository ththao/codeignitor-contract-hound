<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Billing Info Model
 *
 * @access public
 */
class BillingInfoModel extends BaseModel
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
		'billing_info_id'
		,'member_id'
		,'first_name'
		,'last_name'
		,'cc_last_4'
		,'cc_expire'
		,'cc_type'
		,'address'
		,'address2'
		,'city'
		,'state'
		,'zip'
		,'country'
		,'create_date'
		,'stripe_id'
		,'status'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'address2' => ''
		,'cc_type' => self::CCTypeUnknown
		,'status'  => self::STATUS_ACTIVE
	);

	const STATUS_ACTIVE   = 0;
	const STATUS_INVALID  = 1;
	const STATUS_INACTIVE = 2;

	/**
	 * CC Types
	 *
	 * @access protected
	 */
	const CCTypeUnknown         = 0;
	const CCTypeVisa            = 1;
	const CCTypeMastercard      = 2;
	const CCTypeAmericanExpress = 3;
	const CCTypeDiscover        = 4;

	protected $aCCTypes = array(
		self::CCTypeUnknown          => 'Unknown'
		,self::CCTypeVisa            => 'Visa'
		,self::CCTypeMastercard      => 'Mastercard'
		,self::CCTypeAmericanExpress => 'American Express'
		,self::CCTypeDiscover        => 'Discover'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Get Readable CC Types
	 *
	 * @access public
	 * @return string
	 */
	public function getReadableCcType() {
		return $this->_translateField('cc_type',$this->aCCTypes);
	}
}
