<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contract_revisions_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'contract_revisions';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'contract_revision_id';

	public function getFileRevisions($aFilters,$iLimit=5) {
		$this->applyFilters($aFilters);
		$sDB = $this->sDB;
		$this->$sDB->limit($iLimit);

		$this->$sDB->select('DISTINCT(file_hash), file_name, revision_date');
		$this->$sDB->order_by('revision_date','desc');
		return $this->$sDB->get($this->_table)->result_array();
	}
}
