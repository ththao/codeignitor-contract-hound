<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Boards_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'boards';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'board_id';
	
	public function getSubBoardCounts($iParentId,$aBoardIds) {
	    $sDB = $this->sDB;
	    $aParams = array($iParentId);
	    $sQuery =
	    "SELECT `parent_board_id`, COUNT(*) as sub_board_count ".
	    "FROM `boards`".
	    "WHERE `parent_id` = ? AND parent_board_id IS NOT NULL AND parent_board_id IN (" . implode(',', $aBoardIds) . ")".
	    "GROUP BY `parent_board_id`";
	    return $this->$sDB->query($sQuery,$aParams)->result_array();
	}
	
	/**
	 * Get Many Items
	 *
	 * @access public
	 * @param integer $iParentId
	 * @param integer $iMemberId
	 * @param integer $iBoardId
	 * @return array
	 */
	public function getSubBoards($iParentId, $iMemberId, $iBoardId) {
	    $sDB = $this->sDB;
	    $aParams = array($iParentId, $iParentId, $iMemberId, $iMemberId, $iMemberId, $iMemberId, $iParentId, $iBoardId);
	    $sQuery =
	    "SELECT boards.board_id, boards.name, sbs.sub_board_count, cts.contract_count
        FROM boards 
        LEFT OUTER JOIN
        (
            SELECT parent_board_id, COUNT(*) as sub_board_count
    	    FROM boards
    	    WHERE parent_id = ? AND parent_board_id IS NOT NULL
    	    GROUP BY parent_board_id
        ) sbs ON sbs.parent_board_id = boards.board_id
        LEFT OUTER JOIN
        (
            SELECT contracts.board_id, COUNT(*) as contract_count
            FROM contracts
            LEFT JOIN members ON members.member_id = contracts.owner_id AND members.parent_id = ? OR members.member_id = ?
            LEFT JOIN contract_members ON contract_members.contract_id = contracts.contract_id AND contract_members.member_id = ?
            WHERE (contracts.owner_id = ? OR contract_members.member_id = ?) AND contracts.parent_id = ? AND contracts.status = 0
            GROUP BY contracts.board_id
        ) cts ON cts.board_id = boards.board_id
        WHERE boards.parent_board_id = ?
        ORDER BY boards.board_id
        ";
	    return $this->$sDB->query($sQuery,$aParams)->result_array();
	}
}
