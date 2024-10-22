<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Custom_fields_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'custom_fields';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'custom_field_id';

}
