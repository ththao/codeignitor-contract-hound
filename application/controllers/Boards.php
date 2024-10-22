<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Boards extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Contract Validation
	 *
	 * @access protected
	 */
	protected $board_validation = array(
		array(
			'field' => 'name',
			'label' => 'Name',
			'rules' => 'trim|max_length[200]|no_html|required'
		)
	);

	///////////////////////////////////////////////////////////////////////////
	///  Super Methods   /////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function __construct() {
		parent::__construct();

		$this->load->library('form_validation');
	}

	///////////////////////////////////////////////////////////////////////////
	///  Methods   ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Main view contracts
	 *
	 * @access public
	 */
	public function index() {
		$this->load->view('boards/index_lazyload',$this->aData);
	}

	public function search_boards() {
		$iLimit = (empty($_GET['count']) || !is_numeric($_GET['count'])) ? 0 : trim($_GET['count']);
		$iOffset = (empty($_GET['offset']) || !is_numeric($_GET['offset'])) ? 0 : trim($_GET['offset']);
		$sSearchPhrase = (empty($_GET['search_phrase'])) ? null : trim($_GET['search_phrase']);
		//log_message('required',"::search_contracts {$sSearchPhrase} {$iLimit} {$iOffset}");

		$aFilters = array('parent_id'=>$this->_iParentId, 'parent_board_id IS NULL' => null);
		if (!empty($sSearchPhrase)) {
			$aFilters[] = array(
				'method' => 'like'
				,'field' => 'name'
				,'value' => $sSearchPhrase
			);
		}

		//$_SESSION['debug_sql'] = 1;
		$aBoards = Service::load('board')->getBoards($aFilters,'create_date desc',$iLimit,$iOffset)->getResults();
		//unset($_SESSION['debug_sql']);

		if (!empty($aBoards)) {
			$aBoardIds = array();
			foreach ($aBoards as $oBoard) {
				$aBoardIds[] = $oBoard->board_id;
			}
			
			//if ($this->_iParentId == 1) {
			//	$_SESSION['debug_sql'] = 1;
			$aBoardCounts = Service::load('contract')->getBoardContractCountsV2($this->_iMemberId,$this->_iParentId,$aBoardIds)->getResults();
			//	unset($_SESSION['debug_sql']);
			//} else {
			//	$aBoardCounts = Service::load('contract')->getBoardContractCounts($aBoardIds)->getResults();
			//}
			$aBoardsIndexed = array();
			foreach ($aBoardCounts as $aBoardCount) {
				$aBoardsIndexed[$aBoardCount['board_id']] = $aBoardCount['board_count'];
			}
			unset($aBoardCounts);
			
			$subBoardCounts = Service::load('board')->getSubBoardCounts($this->_iParentId,$aBoardIds)->getResults();
			$aSubBoardsIndexed = array();
			foreach ($subBoardCounts as $subBoardCount) {
			    $aSubBoardsIndexed[$subBoardCount['parent_board_id']] = $subBoardCount['sub_board_count'];
			}
			unset($subBoardCounts);

			foreach ($aBoards as $iIndex=>$oBoard) {
				if (isset($aBoardsIndexed[$oBoard->board_id])) {
				    $oBoard->contract_count = $aBoardsIndexed[$oBoard->board_id];
				    $oBoard->sub_folder_count = $aSubBoardsIndexed[$oBoard->board_id];
					$aBoards[$iIndex] = $oBoard;
				}
			}
		}

		$aBoardsFiltered = array();
		foreach ($aBoards as $oBoard) {
			$aBoardsFiltered[] = array(
				'board_id' => $oBoard->board_id,
				'name'     => $oBoard->name,
				'count'    => $oBoard->contract_count == 1?'1 contract':(int)$oBoard->contract_count.' contracts',
			    'sub_folder_count' => $oBoard->sub_folder_count == 1?'1 sub folder':(int)$oBoard->sub_folder_count.' sub folders'
			);
		}

		echo json_encode(array('boards' => $aBoardsFiltered));
	}

	public function view($iBoardId) {
		$oBoard = Service::load('board')->getBoards(array(
			'board_id'   => $iBoardId
			,'parent_id' => $this->_iParentId
		), 'board_id asc', 1)->reset();

		if (empty($oBoard)) {
			$this->session->error('Board not found.');
			redirect('boards');
		}

		$sSortSC = 'cd';
		$sSort = 'create_date desc';
		if (!empty($_GET['s']) && in_array($_GET['s'],array('na','va','aa','ad','sd','ed'))) {
			$sSortSC = $_GET['s'];
			switch ($_GET['s']) {
				case 'na':
					$sSort = 'name asc';
					break;
				case 'va':
					$sSort = 'company asc';
					break;
				case 'aa':
					$sSort = 'valued asc';
					break;
				case 'ad':
					$sSort = 'valued desc';
					break;
				case 'sd':
					$sSort = 'start_date desc';
					break;
				case 'ed':
					$sSort = 'end_date desc';
					break;
			}
		}

		// get contracts, limit 24
		//   get sort
		//$oContracts = Service::load('contract')->getContracts(array(
		//	'board_id'    => $iBoardId
		//	,'status != ' => ContractModel::STATUS_DELETED
		//),$sSort,75);

		$oContracts = Service::load('contract')->searchContracts($this->_iMemberId,$this->_iParentId,'',$iBoardId,'cs.create_date desc',100,0);
		//   get contracts count
		//$iContractCount = Service::load('contract')->getContractCount(array('board_id'=>$iBoardId))->total;
		if (!empty($_GET['debug'])) {
			echo '<pre>'; var_dump($oContracts); return true;
		}

		$aContractIdsWithAccess = array();
		if ($oContracts->count) {
			$aOwnerIds = array();

			$aContractIds = array();
			foreach ($oContracts as $oContract) {
				$aContractIds[] = $oContract->contract_id;
				$aOwnerIds[$oContract->owner_id] = $oContract->owner_id;
				if ($oContract->owner_id == $this->_iMemberId) {
					$aContractIdsWithAccess[] = $oContract->contract_id;
				}
			}

			$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
				'member_id'    => $this->_iMemberId
				,'contract_id' => $aContractIds
			));
			foreach ($oTeamMembers as $oTeamMember) {
				$aContractIdsWithAccess[] = $oTeamMember->contract_id;
			}
			unset($aContractIds);
			unset($oTeamMembers);

			$aOwners = array();
			$oOwners = Service::load('member')->getMembers(array('parent_id'=>$this->_iParentId,'member_id'=>$aOwnerIds));
			foreach ($oOwners as $oOwner) {
				$aOwners[$oOwner->member_id] = $oOwner;
			}

			$this->set('aOwners',$aOwners);
		}
		
		$oSubBoards = Service::load('board')->getSubBoards($this->_iParentId, $this->_iMemberId, $iBoardId);
		$this->set('oSubBoards',$oSubBoards);
		
		$this->session->set_userdata('member_last_page','/boards/view/'.$iBoardId);

		$this->set('sSortSC',$sSortSC);
		$this->set('aContractIdsWithAccess',$aContractIdsWithAccess);
		$this->set('oBoard',$oBoard);
		$this->set('oContracts',$oContracts);
		//$this->set('iContractCount',$iContractCount);
		$this->set('sHeader','View Board');
		$this->build('boards/view_new');
	}

	/**
	 * Add Board
	 *
	 * @access public
	 */
	public function add() {
		$oBS = Service::load('board');

		if ($this->_isPost()) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules($this->board_validation);
			if ($this->form_validation->run()) {
				$oBoard = new BoardModel(array(
					'parent_id'    => $this->_iParentId
					,'create_date' => date('Y-m-d H:i:s')
				));

				foreach ($this->board_validation as $aRule) {
					$oBoard->setField($aRule['field'], set_value($aRule['field']));
				}

				$oAdd = $oBS->addBoard($oBoard);

				if ($oAdd->isOk()) {
				    send_analytic_event('Board Created', null, ['boardId' => $oAdd->first()->board_id, 'boardName' => $oAdd->first()->name]);
				    
					$this->session->success('Folder added.');
					redirect('boards/view/'.$oAdd->first()->board_id);
				} else {
					$this->session->current_error('Unable to add folder.');
				}
			} else {
				$sError = $this->form_validation->first_error();
				//echo '<pre>'; var_dump($sError); exit;
				$this->session->current_error($sError);
			}
		}

		//$this->build('boards/add');
		$this->load->view('boards/add',$this->aData);
	}

	public function remove_contract($iContractId) {
		$oCS = Service::load('contract');
		$oContract = $oCS->getContracts(array('contract_id'=>$iContractId,'parent_id'=>$this->_iParentId))->first();

		if (!empty($oContract)) {
			$iBoardId = $oContract->board_id;
			$oContract->board_id = null;

			$oUpdated = $oCS->updateContract($oContract);

			if ($oUpdated->isOk()) {
			    send_analytic_event('Contract Deleted', null, ['contractId' => $oContract->contract_id, 'contractName' => $oContract->name]);
			    
				$this->session->success('Contract removed.');
			} else {
				$this->session->error('Unable to remove contract from folder.');
			}

			redirect('boards/view/'.$iBoardId);
		} else {
			$this->session->error('Contract not found.');
		}

		redirect('welcome');
	}

	public function rename_board($iBoardId) {
		$oBS = Service::load('board');

		$oBoard = $oBS->getBoards(array(
			'board_id'   => $iBoardId
			,'parent_id' => $this->_iParentId
		), 'board_id asc', 1)->reset();

		if (empty($oBoard)) {
			$this->session->error('Folder not found.');
			redirect('boards');
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->board_validation);
		if ($this->form_validation->run()) {
			foreach ($this->board_validation as $aRule) {
				$oBoard->setField($aRule['field'], set_value($aRule['field']));
			}

			$oUpdate = $oBS->updateBoard($oBoard);

			if ($oUpdate->isOk()) {
				$this->session->success('Folder renamed.');
			} else {
				$this->session->error('Unable to rename folder.');
			}
		} else {
			$sError = $this->form_validation->first_error();
			//echo '<pre>'; var_dump($sError); exit;
			$this->session->error($sError);
		}

		redirect('boards/view/'.$iBoardId);
	}
	
	public function add_sub_board($iBoardId) {
	    $oBS = Service::load('board');
	    
	    $oBoard = $oBS->getBoards(array(
	        'board_id'   => $iBoardId
	        ,'parent_id' => $this->_iParentId
	    ), 'board_id asc', 1)->reset();
	    
	    if (empty($oBoard)) {
	        $this->session->error('Parent folder not found.');
	        redirect('boards');
	    }
	    
	    $this->load->library('form_validation');
	    $this->form_validation->set_rules($this->board_validation);
	    if ($this->form_validation->run()) {
	        $oSubBoard = new BoardModel(array(
	            'parent_id' => $this->_iParentId,
	            'parent_board_id' => $iBoardId,
	            'create_date' => date('Y-m-d H:i:s')
	        ));
	        
	        foreach ($this->board_validation as $aRule) {
	            $oSubBoard->setField($aRule['field'], set_value($aRule['field']));
	        }
	        
	        $oAdd = $oBS->addBoard($oSubBoard);
	        
	        if ($oAdd->isOk()) {
	            send_analytic_event('Board Created', null, ['boardId' => $oAdd->first()->board_id, 'boardName' => $oAdd->first()->name]);
	            
	            $this->session->success('Sub folder added.');
	            redirect('boards/view/'.$iBoardId);
	        } else {
	            $this->session->error('Unable to add sub folder.');
	        }
	    } else {
	        $sError = $this->form_validation->first_error();
	        $this->session->error($sError);
	    }
	    
	    redirect('boards/view/'.$iBoardId);
	}

	public function delete($iBoardId) {
		$oBS = Service::load('board');

		$oBoard = $oBS->getBoards(array(
			'board_id'   => $iBoardId
			,'parent_id' => $this->_iParentId
		), 'board_id asc', 1)->reset();

		if (empty($oBoard)) {
			$this->session->error('Folder not found.');
			redirect('boards');
		}

		// reassign contracts
		Service::load('contract')->updateContracts(array('board_id'=>$iBoardId),array('board_id'=>null));

		// delete board
		$oDeleted = $oBS->deleteBoards(array('board_id'=>$iBoardId));
		if ($oDeleted->isOk()) {
		    send_analytic_event('Board Deleted', null, ['boardId' => $oBoard->board_id, 'boardName' => $oBoard->name]);
		    
			$this->session->success('Folder deleted');
			redirect('boards');
		}

		$this->session->error('Unable to delete folder');
		redirect('boards/view/'.$iBoardId);
	}
}
