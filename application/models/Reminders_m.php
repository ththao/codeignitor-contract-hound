<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Reminders_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'reminders';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'reminder_id';

	public function getRemindersByTeamMember($iMemberId,$iParentId,$active,$sOrderBy='alert_date asc',$iLimit=7,$iOffset=0) {
		$sDB = $this->sDB;

		$sQuery =
			"SELECT `reminders`.*, `contracts`.`name` ".
			"FROM `reminders` ".
				"LEFT JOIN (SELECT * FROM `contract_members` WHERE `member_id` = ?) cm ON cm.`contract_id` = `reminders`.`contract_id` ".
				"LEFT JOIN `contracts` ON `contracts`.`contract_id` = `reminders`.`contract_id` ".
			"WHERE (cm.`member_id` = ? OR `contracts`.`owner_id` = ?) ".
				"AND `reminders`.`status` = ? AND `contracts`.`parent_id` = ? ORDER BY {$sOrderBy}";
		
		if ($active) {
		    $sQuery .= " LIMIT ?, ?";
		    $query = $this->$sDB->query($sQuery,array($iMemberId,$iMemberId,$iMemberId,$active,$iParentId,$iOffset,$iLimit));
		} else {
		    $query = $this->$sDB->query($sQuery,array($iMemberId,$iMemberId,$iMemberId,$active,$iParentId));
		}
		return $query->result_array();
	}
}
