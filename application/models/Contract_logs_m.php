<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contract_logs_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'contract_logs';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'contract_log_id';

	public function getContractLogsByTeamMember($iMemberId,$iParentId,$sOrderBy='alert_date asc',$iLimit=20,$iOffset=0) {
		$sDB = $this->sDB;

		$sQuery =
			"SELECT `contract_logs`.*, `contracts`.`name` ".
			"FROM `contract_logs` ".
				"LEFT JOIN (SELECT * FROM `contract_members` WHERE `member_id` = ?) cm ON cm.`contract_id` = `contract_logs`.`contract_id` ".
				"LEFT JOIN `contracts` ON `contracts`.`contract_id` = `contract_logs`.`contract_id` ".
			"WHERE (cm.`member_id` = ? OR `contracts`.`owner_id` = ?) AND `contracts`.`parent_id` = ? ".
				"ORDER BY {$sOrderBy} LIMIT ?, ?";

		return $this->$sDB->query($sQuery,array($iMemberId,$iMemberId,$iMemberId,$iParentId,$iOffset,$iLimit))->result_array();
	}
}
