<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class BoardService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'BoardModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add Board
	 *
	 * @access public
	 * @param BoardModel $oBoard
	 * @return ServiceResponse
	 */
	public function addBoard(BoardModel $oBoard) {
		$iResult = $this->_getModel('boards_m')->addItem($oBoard->toArray());
		if ($iResult) {
			$oBoard->board_id = $iResult;
			$oBoard->isSaved(true);
			return new ServiceResponse(array($oBoard));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Boards
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteBoards($aFilters=array()) {
		$bDelete = $this->_getModel('boards_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get Board
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getBoard($aFilters=array()) {
		$aBoard = $this->_getModel('boards_m')->getItem($aFilters);
		if (!empty($aBoard)) {
			return $this->_setupResponse(array($aBoard));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Board Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getBoardCount($aFilters=array()) {
		$iCount = $this->_getModel('boards_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}
	
	public function getSubBoardCounts($iParentId,$aBoardIds) {
	    if (empty($aBoardIds)) {
	        return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	    }
	    $aCounts = $this->_getModel('boards_m')->getSubBoardCounts($iParentId,$aBoardIds);
	    return new ServiceResponse($aCounts);
	}
	
	public function getSubBoards($iParentId, $iMemberId, $aBoardId) {
	    $aBoards = $this->_getModel('boards_m')->getSubBoards($iParentId, $iMemberId, $aBoardId);
	    return $this->_setupResponse($aBoards);
	}

	/**
	 * Get Boards
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getBoards($aFilters=array(),$sSort='board_id asc',$iLimit=null,$iOffset=null) {
		$aBoards = $this->_getModel('boards_m')->getItems($aFilters,$sSort,$iLimit,$iOffset);
		return $this->_setupResponse($aBoards);
	}

	/**
	 * Update Board
	 *
	 * @access public
	 * @param BoardModel $oBoard
	 * @return ServiceResponse
	 */
	public function updateBoard(BoardModel $oBoard) {
		if (!$oBoard->board_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('boards_m')->updateItem($oBoard->toArray());
		if ($bResponse) {
			$oBoard->isSaved(true);
			return new ServiceResponse(array($oBoard));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
