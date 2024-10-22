<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contract_approvals_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'contract_approvals';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'contract_approval_id';

	public function getContractApprovalsWithAssignees($iContractId,$sOrderBy='step asc',$iLimit=20,$iOffset=0) {
		$sDB = $this->sDB;

		$sQuery =
			"SELECT `contract_approvals`.*, `members`.`email`, `members`.`first_name`, `members`.`last_name`, `members`.`role`, `members`.`avatar` ".
			"FROM `contract_approvals` ".
				"LEFT JOIN `members` ON `contract_approvals`.`member_id` = `members`.`member_id` ".
			"WHERE (`contract_approvals`.`contract_id` = ?) ".
				"ORDER BY {$sOrderBy} LIMIT ?, ?";

		return $this->$sDB->query($sQuery,array($iContractId,$iOffset,$iLimit))->result_array();
	}

}
