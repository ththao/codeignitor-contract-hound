<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Reminder_members_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'reminder_members';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'reminder_member_id';
}
