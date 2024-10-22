<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Docusign_access_token_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'docusign_access_tokens';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'docusign_access_token_id';
}
