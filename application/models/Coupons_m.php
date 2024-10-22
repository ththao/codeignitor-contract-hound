<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Coupons_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'coupons';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'coupon_id';

}
