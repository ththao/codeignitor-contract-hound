<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Board Model
 *
 * @access public
 */
class BoardModel extends BaseModel
{
	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Fields for model
	 *
	 * @access protected
	 */
	protected $aFields = array(
		'board_id'
	    ,'parent_id'
	    ,'parent_board_id'
		,'name'
		,'create_date'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
    
	public function getParentBoard()
	{
	    if ($this->parent_board_id) {
	        $oParentBoard = Service::load('board')->getBoards(array(
	            'board_id'   => $this->parent_board_id
	        ), 'board_id asc', 1)->reset();
	        
	        return $oParentBoard;
	    }
	    
	    return null;
	}
	
	public function getSubBoards()
	{
	    return Service::load('board')->getBoards(array(
	        'parent_board_id' => $this->board_id
	    ),'name asc',100);
	}
	
	public function getSubBoardOptions()
	{
	    $html = '';
	    $subBoards = $this->sub_boards;
	    if ($subBoards) {
	        foreach ($subBoards as $subBoard) {
	            $html .= '<option value="' . $subBoard->board_id . '">' . $subBoard->board_path . '</option>';
	            $html .= $subBoard->sub_board_options;
	        }
	    }
	    return $html;
	}
	
	public function getParentLink()
	{
	    $parent = $this->getParentBoard();
	    if ($parent) {
	        return $parent->parent_link . '<a href="/boards/view/' . $parent->board_id . '">' . $parent->name . '</a>&nbsp;&gt;&nbsp;';
	    }
	    return '';
	}
	
	public function getBoardPath()
	{
	    $parent = $this->getParentBoard();
	    if ($parent) {
	        return $parent->board_path . ' > ' . $this->name;
	    }
	    return $this->name;
	}
}
