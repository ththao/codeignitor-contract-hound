<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Docusign_contracts_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'docusign_contracts';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'docusign_contract_id';


	public function getDocusignContractsForAccount($iParentId,$iStatus) {
		$sDB = $this->sDB;
		$aParams = array($iParentId,$iStatus);
		$sQuery =
			"SELECT * ".
			"FROM `contracts` cs ".
				"LEFT JOIN `docusign_contracts` dct ON dct.`contract_id` = cs.`contract_id` ".
			"WHERE cs.`parent_id` = ? AND dct.`status` = ? ".
			"LIMIT 50";
		return $this->$sDB->query($sQuery,$aParams)->result_array();
	}
}
