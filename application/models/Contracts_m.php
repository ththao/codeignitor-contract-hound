<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contracts_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'contracts';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'contract_id';

	public function getBoardContractCounts($aBoardIds) {
		$sDB = $this->sDB;
		$this->$sDB->select('board_id, count(*) as board_count');
		$this->$sDB->where('status !=',3); // deleted
		$this->$sDB->where_in('board_id',$aBoardIds);
		$this->$sDB->group_by('board_id');
		return $this->$sDB->get($this->_table)->result_array();
	}

	public function getBoardContractCountsV2($iMemberId,$iParentId,$aBoardIds) {
		$sDB = $this->sDB;
		$aParams = array($iParentId,$iMemberId,$iMemberId,$iParentId,$aBoardIds,$iMemberId,$iMemberId,$iParentId);
		$sQuery =
			"SELECT cs.`board_id`, COUNT(*) as board_count ".
			"FROM `contracts` cs ".
				"LEFT JOIN (SELECT * FROM `members` WHERE `parent_id` = ? OR `member_id` = ?) cm ON cm.`member_id` = cs.`owner_id` ". // member parent table now
				"LEFT JOIN `contract_members` cmt ON cmt.`contract_id` = cs.`contract_id` AND cmt.`member_id` = ? ".
				"LEFT JOIN (SELECT * FROM `boards` WHERE `parent_id` = ? AND `board_id` IN ?) bt ON bt.`board_id` = cs.`board_id` ".
			"WHERE (cs.`owner_id` = ? OR cmt.`member_id` = ?) AND cs.`parent_id` = ? AND cs.`status` = 0 ".
			"GROUP BY cs.`board_id`";
		
		return $this->$sDB->query($sQuery,$aParams)->result_array();
	}
	
	public function countContracts($iMemberId,$iParentId,$iBoardId=null) {
	    $sDB = $this->sDB;
	    
	    $sQuery =
	    "SELECT COUNT(*) AS contract_count ".
	    "FROM `contracts` cs ";
	    $sQuery .=
	    "LEFT JOIN `contract_members` cmt ON cmt.`contract_id` = cs.`contract_id` AND cmt.`member_id` = ? ".
	    "WHERE (cs.`owner_id` = ? OR cmt.`member_id` = ? OR cs.`parent_id` = ?) AND cs.`status` = 0 AND cs.`parent_id` = ? ";
	    
	    $aParams = array($iMemberId,$iMemberId,$iMemberId,$iMemberId,$iParentId);
	    
	    if (!empty($iBoardId) && is_numeric($iBoardId)) {
	        $sQuery .=
	        " AND cs.`board_id` = ?";
	        $aParams[] = $iBoardId;
	    }
	    
	    $result = $this->$sDB->query($sQuery,$aParams)->row_array();
	    
	    return $result['contract_count'];
	}

	public function searchContracts($iMemberId,$iParentId,$sSearchTerm,$iBoardId=null,$sOrderBy='cs.create_date desc',$iLimit=20,$iOffset=0) {
		$sDB = $this->sDB;

		$aParams = array($iParentId,$iMemberId);
		$sQuery =
			"SELECT cs.*, cm.`email`, cm.`first_name`, cm.`last_name`, cm.`avatar`, cm.`member_id` ".
			"FROM `contracts` cs ".
				"LEFT JOIN (SELECT * FROM `members` WHERE `parent_id` = ? OR `member_id` = ?) cm ON cm.`member_id` = cs.`owner_id` "; // member parent table now

		if (!empty($sSearchTerm)) {
			$sQuery .= 
				"LEFT JOIN (SELECT COUNT(*) as count_field_value_match, contract_id FROM `custom_field_value_text` ".
					"WHERE `parent_id` = ? AND `field_value` like ? GROUP BY `contract_id`) cfvt ON cfvt.`contract_id` = cs.`contract_id` ";
			$aParams[] = $iParentId;
			$aParams[] = "%$sSearchTerm%";
		}

		$sQuery .=
				"LEFT JOIN `contract_members` cmt ON cmt.`contract_id` = cs.`contract_id` AND cmt.`member_id` = ? ".
			"WHERE (cs.`owner_id` = ? OR cmt.`member_id` = ? OR cs.`parent_id` = ?) AND cs.`status` = 0 AND cs.`parent_id` = ? ";
		$aParams = array_merge($aParams,array($iMemberId,$iMemberId,$iMemberId,$iMemberId,$iParentId));

		if (!empty($sSearchTerm)) {
			$sQuery .=
				"AND ((cs.`name` like ?) OR (cm.`first_name` like ?) OR (cm.`last_name` like ?) OR (cm.`email` like ?) OR (cs.`company` like ?) OR (cfvt.`count_field_value_match` > 0))";
			$aParams[] = "%$sSearchTerm%";
			$aParams[] = "%$sSearchTerm%";
			$aParams[] = "%$sSearchTerm%";
			$aParams[] = "%$sSearchTerm%";
			$aParams[] = "%$sSearchTerm%";
		}

		if (!empty($iBoardId) && is_numeric($iBoardId)) {
			$sQuery .=
				" AND cs.`board_id` = ?";
			$aParams[] = $iBoardId;
		}

		$sQuery .=
				" ORDER BY {$sOrderBy} LIMIT ?, ?";
		$aParams[] = (int) $iOffset;
		$aParams[] = (int) $iLimit;
		return $this->$sDB->query($sQuery,$aParams)->result_array();
	}
	
	public function exportContracts($aFilters=array(),$mOrderBy='',$iLimit=0,$iOffset=0) {
	    $sDB = $this->sDB;
	    
	    $this->$sDB->select("contracts.*, members.email, members.first_name, members.last_name, rmds.active_r, rmds.expired_r");
	    $this->$sDB->from($this->_table);
	    $this->$sDB->join('members', 'members.member_id = contracts.owner_id', 'LEFT OUTER');
	    $this->$sDB->join(
	        '(SELECT contract_id, SUM(IF(status = 1, 1, 0)) AS active_r, SUM(IF(status = 1, 0, 1)) AS expired_r FROM reminders GROUP BY contract_id) AS rmds', 
	        'rmds.contract_id = contracts.contract_id', 
	        'LEFT OUTER'
        );
	    
	    if (!empty($iLimit) && !empty($iOffset)) {
	        $this->$sDB->limit($iLimit,$iOffset);
	    } elseif (!empty($iLimit)) {
	        $this->$sDB->limit($iLimit);
	    }
	    
	    if (!empty($mOrderBy)) {
	        if (is_array($mOrderBy)) {
	            foreach ($mOrderBy as $aSort) {
	                $this->$sDB->order_by($aSort[0],$aSort[1]);
	            }
	        } else {
	            $this->$sDB->order_by($mOrderBy);
	        }
	    } else {
	        $this->$sDB->order_by($this->primary_key,'asc');
	    }
	    
	    $this->applyFilters($aFilters);
	    $query = $this->$sDB->get();
	    
	    return $query->result_array();
	}

	public function getContractsByTeamMember($iMemberId,$iParentId,$sOrderBy='cs.create_date desc',$iLimit=20,$iOffset=0) {
		$sDB = $this->sDB;

		$sQuery =
			"SELECT cs.*, cm.level ".
			"FROM `contracts` cs ".
				"LEFT JOIN (SELECT * FROM `contract_members` WHERE `member_id` = ?) cm ON cm.`contract_id` = cs.`contract_id` ".
			"WHERE (cm.`member_id` = ? OR cs.`owner_id` = ?) AND cs.`parent_id` = ? AND cs.`status` = 0 ".
				"ORDER BY {$sOrderBy} LIMIT ?, ?";

		return $this->$sDB->query($sQuery,array($iMemberId,$iMemberId,$iMemberId,$iParentId,$iOffset,$iLimit))->result_array();
	}

	public function getContractCountsByMember($aMemberIds) {
		if (empty($aMemberIds) || !is_array($aMemberIds)) {
			return false;
		}

		$sDB = $this->sDB;
		$this->$sDB->select('owner_id, count(*) as contract_count');
		$this->$sDB->where_in('owner_id',$aMemberIds);
		$this->$sDB->where('status', 0);
		$this->$sDB->group_by('owner_id');
		return $this->$sDB->get($this->_table)->result_array();
	}
}
