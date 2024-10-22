<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Settings_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'settings';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'setting_id';

}
