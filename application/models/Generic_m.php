<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Generic_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = '';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = '';

	public function init(GenericModel $oModel) {
		$this->_table = $oModel->table;
		$this->primary_key = $oModel->primary_key;
		return $this;
	}
}
