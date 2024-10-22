<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contract_signatures_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'contract_signatures';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'contract_signature_id';
}
