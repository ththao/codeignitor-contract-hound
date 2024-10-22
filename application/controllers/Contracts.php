<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contracts extends User_Controller {

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

	/**
	 * Contract Validation
	 *
	 * @access protected
	 */
	protected $contract_validation = array(
		array(
			'field' => 'name',
			'label' => 'Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'company',
			'label' => 'Company',
			'rules' => 'trim|max_length[200]'
		),
		array(
			'field' => 'valued',
			'label' => 'Value',
			'rules' => 'trim|callback__strip_extra_chars|max_length[20]|empty_to_null'
		),
		array(
			'field' => 'type',
			'label' => 'type',
			'rules' => 'trim|callback__valid_type'
		),
		array(
			'field' => 'start_date',
			'label' => 'Start Date',
			'rules' => 'trim|empty_to_null'
		),
		array(
			'field' => 'end_date',
			'label' => 'End Date',
			'rules' => 'trim|empty_to_null'
		),
		/*array(
			'field' => 'board_id',
			'label' => 'Board',
			'rules' => 'trim|integer|empty_to_null'
		),*/
	);

	/**
	 * Contract Log Validation
	 *
	 * @access protected
	 */
	protected $contract_log_validation = array(
		array(
			'field' => 'message',
			'label' => 'message',
			'rules' => 'trim|required|max_length[255]'
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
	 * Strip extra characters
	 *
	 * @access public
	 * @param number $iValue
	 * @return int
	 */
	public function _strip_extra_chars($iValue) {
		return str_replace(array('$',','),'',trim($iValue));
	}

	/**
	 * Validate and convert date
	 *
	 * @access public
	 * @param string $sDate
	 * @param string $sFormat
	 * @return bool
	 */
	public function _check_date($sDate,$sFormat='n/j/Y m/d/Y') {
		if (empty($sDate)) {
			return true;
		}

		$iDate = strtotime($sDate);

		$aDateOptions = explode(' ',$sFormat);
		foreach ($aDateOptions as $sDateOption) {
			$sFormattedDate = date($sDateOption, $iDate);

			//log_message('required',"::_check_date comparing {$sDate} to {$sFormattedDate}");
			if (strcmp($sDate, $sFormattedDate) === 0) {
				//log_message('required',"::_check_date passed {$sDate} to {$sFormattedDate}");
				return date('Y-m-d 00:00:00', $iDate);
			}
		}

		//log_message('required',"::_check_date failed {$sDate}");
		$this->form_validation->set_message('_check_date', 'Invalid Date');
		return false;
	}

	/**
	 * Validate Contract Type
	 *
	 * @access public
	 * @param mixed $iType
	 * @return bool
	 */
	public function _valid_type($iType) {
		if (!is_numeric($iType) || !in_array($iType,array(0,1))) {
			$this->form_validation->set_message('_valid_type', 'Invalid Type');
			return false;
		}

		return true;
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
	    $this->load->model('contracts_m');
	    $this->set('contractCount', $this->contracts_m->countContracts($this->_iMemberId, $this->_iParentId, null));
	    
	    $oCustomFields = Service::load('customfield')->getCustomFields(array('parent_id'=>$this->_iParentId));
	    $this->set('oCustomFields',$oCustomFields);
	    
	    $this->load->view('contracts/index_datatable', $this->aData);
	}
	
	public function search_contracts() {
	    $iLimit = (empty($_GET['count']) || !is_numeric($_GET['count'])) ? 0 : trim($_GET['count']);
	    $oContracts = Service::load('contract')->searchContracts($this->_iMemberId,$this->_iParentId,'',null,'cs.create_date desc',$iLimit,0);
	    
	    $aContractIds = array();
	    foreach ($oContracts as $oContract) {
	        $aContractIds[] = $oContract->contract_id;
	    }
	    
	    $oTeamMembers = array();
	    if (count($aContractIds)) {
	        $oTeamMembers = Service::load('contractmember')->getContractMembers(array(
	            'member_id'    => $this->_iMemberId
	            ,'contract_id' => $aContractIds
	        ));
	    }
	    $aContractIdsWithAccess = array();
	    foreach ($oTeamMembers as $oTeamMember) {
	        $aContractIdsWithAccess[] = $oTeamMember->contract_id;
	    }
	    unset($aContractIds);
	    unset($oTeamMembers);
	    
	    $oCustomFields = Service::load('customfield')->getCustomFields(array('parent_id'=>$this->_iParentId));
	    $this->set('oCustomFields',$oCustomFields);
	    
	    $data = array();
	    foreach ($oContracts as $oContract) {
	        $sOwnerName = trim($oContract->first_name.' '.$oContract->last_name);
	        if (empty($sOwnerName)) {
	            $sOwnerName = $oContract->email;
	        }
	        
	        $status = [
	            'protected' => ($oContract->owner_id == $this->_iMemberId || in_array($oContract->contract_id,$aContractIdsWithAccess))?false:true,
	            'error'     => false,
	            'new'       => ($oContract->create_date > date('Y-m-d H:i:s',strtotime('-30 days'))) ? true: false,
	            'over'      => false
	        ];
	        $type = $oContract->type ? tl('Buy-side') : tl('Sell-side');
	        $start = $oContract->start_date ? convertto_local_datetime($oContract->start_date,$this->cTimeZone,'%x') : '';
	        $end = $oContract->end_date ? convertto_local_datetime($oContract->end_date,$this->cTimeZone,'%x') : '';
	        $avatar = $oContract->avatar ? "/uas/{$oContract->avatar}" : '/ui/img/avatars/default.png';
	        
	        $row = [
	            'contract_id' => $oContract->contract_id,
	            'is_new' => $status['new'],
	            'vendor_filter' => $oContract->company,
	            'owner_filter' => $sOwnerName,
	            'name' => '<a href="/contracts/view/' . $oContract->contract_id . '" class="cell-link ' . ($status['protected'] ? 'cell-protected' : '') . '" title="' . $oContract->name . '">' . $oContract->name .
	            ($status['over'] ? '<span class="label label-warning">over quota</span>' : '') .
	            ($status['error'] ? ('<span class="label label-danger">' . $status['error'] . '</span>') : '') .
	            ($status['new'] ? '<span class="label label-success">new</span>' : '') . '</a>',
	            'vendor' => $oContract->company,
	            'amount' => $oContract->valued ? $this->aData['sCurrency'].$oContract->valued : '',
	            'type' => $type,
	            'start' => $start,
	            'end' => $end,
	            'owner_name' => '<a href="/users/profile' . (($this->aData['iCurrentlyLoggedInMemberId'] == $this->aData['iCurrentlyLoggedInParentId']) ? '_admin' : '') . '/' . $oContract->member_id . '" class="cell-link alternate" title="' . $sOwnerName . '">' . $sOwnerName . '</a>',
	            'owner_avatar' => '<a href="/users/profile' . (($this->aData['iCurrentlyLoggedInMemberId'] == $this->aData['iCurrentlyLoggedInParentId']) ? '_admin' : '') . '/' . $oContract->member_id . '" class="cell-link"><div class="avatar" ng-style="background-image: url(' . $avatar . ')"><img ng-src="' . $avatar . '" /></div></a>'
	        ];
	        
	        $oCustomFieldValueTexts = Service::load('customfieldvaluetext')->getCustomFieldValueTexts(array(
	            'parent_id'    => $this->_iParentId
	            ,'contract_id' => $oContract->contract_id
	        ));
	        $oCustomFieldValueCheckboxes = Service::load('customfieldvaluecheckbox')->getCustomFieldValueCheckboxes(array(
	            'parent_id'    => $this->_iParentId
	            ,'contract_id' => $oContract->contract_id
	        ));
	        
	        foreach ($oCustomFields as $oCustomField) {
	        
    	        $mValue = $oCustomField->default_value;
    	        if ($oCustomField->type==CustomFieldModel::TYPE_CHECKBOX) {
    	            $mValue = 0;
    	            foreach ($oCustomFieldValueCheckboxes as $oCustomFieldValueCheckbox) {
    	                if ($oCustomFieldValueCheckbox->custom_field_id == $oCustomField->custom_field_id) {
    	                    $mValue = 1;
    	                }
    	            }
    	        } else {
    	            foreach ($oCustomFieldValueTexts as $oCustomFieldValueText) {
    	                if ($oCustomFieldValueText->custom_field_id == $oCustomField->custom_field_id) {
    	                    $mValue = $oCustomFieldValueText->field_value;
    	                }
    	            }
    	        }
    	        if ($oCustomField->type==CustomFieldModel::TYPE_MULTILINE) {
    	            $value = wordwrap(retud($mValue),45,'<br/>',true);
    	        } elseif ($oCustomField->type==CustomFieldModel::TYPE_TEXT) {
    	            $value = wordwrap(retud($mValue),45,'<br/>',true);
    	        } else {
    	            $value = ($mValue?'yes':'no');
				}
				
				$row[$oCustomField->label_field] = $value;
			}
			
			$data[] = $row;
	    }
	    
	    $output = array(
	        "recordsTotal" => count($data),
	        "recordsFiltered" => count($data),
	        "data" => $data
	    );
	    echo json_encode($output);
	    exit();
	}

	/**
	 * Add Contract
	 *
	 * @access public
	 */
	public function add() {
		if ($this->_isPost()) {
			$this->form_validation->set_rules($this->contract_validation);
			if (!empty($_FILES['contract_file']['size']) && $this->form_validation->run()) {
				$oContract = new ContractModel(array(
					'owner_id'      => $this->_iMemberId
					,'parent_id'    => $this->_iParentId
					,'create_date'  => date('Y-m-d H:i:s')
					,'last_updated' => date('Y-m-d H:i:s')
				));

				foreach ($this->contract_validation as $aRule) {
					$oContract->setField($aRule['field'], set_value($aRule['field']));
				}

				if (empty($oContract->company)) {
					$oContract->company = '';
				}

				$oContract->file_name = $_FILES['contract_file']['name'];
				$oContract->enct = 1;
				$oAdd = Service::load('contract')->addContract($oContract, $_FILES['contract_file']['tmp_name']);

				if ($oAdd->isOk()) {
					$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
						'member_id'    => $this->_iMemberId
						,'contract_id' => $iContractId
						,'message'     => 'added contract'
						,'type'        => ContractLogModel::TYPE_UPDATE
						,'create_date' => date('Y-m-d H:i:s')
					)));

					Service::load('contractrevision')->generateRevision($oContract);
					
					send_analytic_event('Contract Created', null, ['contractId' => $oAdd->first()->contract_id, 'contractName' => $oAdd->first()->name]);
					
					$this->session->success('Contract added.');
					redirect('contracts');
				} else {
					$this->session->current_error('Unable to add contract.');
				}
			} elseif (empty($_FILES['contract_file']['size'])) {
				$this->session->current_error('A file is required.');
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		$this->build('contracts/add');
	}

	public function change_board($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->reset();

		if (empty($oContract)) {
			$this->session->error('Contract not found. EC:CNF');
			$this->_redirectLast('contracts');
		}

		if ($this->_isPost() && !empty($_POST['board_id']) && is_numeric($_POST['board_id'])) {
			$iBoardId = trim($_POST['board_id']);

			$oBoard = Service::load('board')->getBoards(array(
				'board_id'   => $iBoardId
				,'parent_id' => $this->_iParentId
			))->reset();

			if (!empty($oBoard)) {
				$oContract->board_id = $oBoard->board_id;
				Service::load('contract')->updateContract($oContract);
				$this->session->success('Contract moved to new folder.');
				redirect('contracts/view/'.$oContract->contract_id);
			} else {
				$this->session->current_error('Folder not found.');
			}
		}

		$oBoards = Service::load('board')->getBoards(array(
			'parent_id' => $this->_iParentId,
		    'parent_board_id IS NULL' => null
		),'name asc',100);

		$this->aData['oContract'] = $oContract;
		$this->aData['oBoards'] = $oBoards;
		$this->load->view('boards/change_boards',$this->aData);
	}

	protected function _validateCustomFields($oCustomFields) {
		$aCustomFieldValues = !empty($_POST['cf'])?$_POST['cf']:array();
		foreach ($oCustomFields as $oCustomField) {
			$mValue = trim(!empty($aCustomFieldValues[$oCustomField->custom_field_id])?$aCustomFieldValues[$oCustomField->custom_field_id]:'');
			if ($oCustomField->required && empty($mValue)) {
				$this->form_validation->set_error(retud($oCustomField->label_text),retud($oCustomField->label_text).' is required.');
				return false;
			}

			$sCleanValue = strip_tags($mValue);
			if (strcmp($sCleanValue,$mValue)!==0) {
				$this->form_validation->set_error(retud($oCustomField->label_text),retud($oCustomField->label_text).' does not support HTML.');
				return false;
			}

			if (strlen($mValue) > 2000) {
				$this->form_validation->set_error(retud($oCustomField->label_text),retud($oCustomField->label_text).' is too long. Max length 2000 characters.');
				return false;
			}
		}

		return true;
	}

	/**
	 * Update a Contract
	 *
	 * @access public
	 * @param int $iContractId
	 */
	public function edit($iContractId) {
		$oCS = Service::load('contract');
		$oContract = $oCS->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oCustomFields = Service::load('customfield')->getCustomFields(array('parent_id'=>$this->_iParentId));
		if ($this->_isPost()) {
			//log_message('required',$this->_iMemberId.' contract::edit $_POST '.print_r($_POST,true));
			$this->form_validation->set_rules($this->contract_validation);
			if ($this->form_validation->run() && $this->_validateCustomFields($oCustomFields)) {
				foreach ($this->contract_validation as $aRule) {
					if($aRule['field'] == 'start_date' || $aRule['field'] == 'end_date'){
						$postValue = $this->input->post($aRule['field']) != '' ? $this->input->post($aRule['field']) : $oContract->defaultValue($aRule['field']);
						if($this->input->post($aRule['field']) != '') {
							$oContract->setField($aRule['field'], convert_utc_datetime($this->input->post($aRule['field']), $this->cTimeZone), $oContract->defaultValue($aRule['field']));
						}else{
							$oContract->setField($aRule['field'],NULL);
						}
					}else{
						$oContract->setField($aRule['field'], $this->form_validation->set_value($aRule['field'],$oContract->defaultValue($aRule['field'])));
					}

				}
				if ($oContract->valued === '') {
					$oContract->valued = null;
				}

				//update custom fields
				$oCFVTS = Service::load('customfieldvaluetext');
				$oCustomFieldValueTexts = $oCFVTS->getCustomFieldValueTexts(array(
					'parent_id'    => $this->_iParentId
					,'contract_id' => $iContractId
				));
				Service::load('customfieldvaluecheckbox')->deleteCustomFieldValueCheckboxes(array(
					'parent_id'    => $this->_iParentId
					,'contract_id' => $iContractId
				));

				$aCustomFieldValues = !empty($_POST['cf'])?$_POST['cf']:array();
				foreach ($oCustomFields as $oCustomField) {
					$mValue = trim(!empty($aCustomFieldValues[$oCustomField->custom_field_id])?$aCustomFieldValues[$oCustomField->custom_field_id]:'');
					if (empty($mValue)) {
						foreach ($oCustomFieldValueTexts as $oCustomFieldValueText) {
							if ($oCustomFieldValueText->custom_field_id == $oCustomField->custom_field_id) {
								$oCFVTS->deleteCustomFieldValueTexts(array(
									'custom_field_value_text_id' => $oCustomFieldValueText->custom_field_value_text_id
								));
							}
						}
					} else {
						if ($oCustomField->type == CustomFieldModel::TYPE_CHECKBOX) {
							if (!empty($aCustomFieldValues[$oCustomField->custom_field_id])) {
								Service::load('customfieldvaluecheckbox')->addCustomFieldValueCheckbox(new CustomFieldValueCheckboxModel(array(
									'parent_id'        => $this->_iParentId
									,'contract_id'     => $iContractId
									,'custom_field_id' => $oCustomField->custom_field_id
									,'field_value'     => 1
								)));
							}
							continue;
						}
						$bFound = false;
						foreach ($oCustomFieldValueTexts as $oCustomFieldValueText) {
							if ($oCustomFieldValueText->custom_field_id == $oCustomField->custom_field_id) {
								$bFound = true;
								$oCustomFieldValueText->field_value = $mValue;
								$oCFVTS->updateCustomFieldValueText($oCustomFieldValueText);
							}
						}

						if (!$bFound) {
							$oCFVTS->addCustomFieldValueText(new CustomFieldValueTextModel(array(
								'parent_id'        => $this->_iParentId
								,'contract_id'     => $iContractId
								,'custom_field_id' => $oCustomField->custom_field_id
								,'field_value'     => $mValue
							)));
						}
					}
				}

				$oContract->last_updated = date('Y-m-d H:i:s');
				if (!empty($_FILES['contract_file']['size'])) {
					$oContract->file_name = $_FILES['contract_file']['name'];
					$oUpdate = Service::load('contract')->updateContract($oContract, $_FILES['contract_file']['tmp_name']);
				} else {
					$oUpdate = Service::load('contract')->updateContract($oContract);
				}

				if ($oUpdate->isOk()) {
					$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
						'member_id'    => $this->_iMemberId
						,'contract_id' => $iContractId
						,'message'     => 'updated contract'
						,'type'        => ContractLogModel::TYPE_UPDATE
						,'create_date' => date('Y-m-d H:i:s')
					)));

					Service::load('contractrevision')->generateRevision($oContract);
					$this->session->success('Contract updated.');
					redirect('contracts/view/'.$iContractId);
				} else {
					$this->session->current_error('Unable to update contract.');
				}
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
		));

		$aTeamMemberIds = array($oContract->owner_id);
		foreach ($oTeamMembers as $oTeamMember) {
			$aTeamMemberIds[] = $oTeamMember->member_id;
		}

		$oMembers = new ServiceResponse();
		$aTeamMembers = array();
		if (!empty($aTeamMemberIds)) {
			$oMembers = Service::load('member')->getMembers(array(
				'member_id'  => $aTeamMemberIds
				,'parent_id' => $this->_iParentId
			));
			foreach ($oMembers as $oMember) {
				if ($oContract->owner_id == $oMember->member_id) {
					$oMember->level = 'Owner';
				} else {
					foreach ($oTeamMembers as $oTeamMember) {
						if ($oTeamMember->member_id == $oMember->member_id) {
							$oMember->level = $oTeamMember->readable_level;
						}
					}
				}
				$aTeamMembers[$oMember->member_id] = $oMember;
			}
		}

		$oReminders = Service::load('reminder')->getReminders(array(
			'contract_id'    => $iContractId
			,'alert_date >=' => date('Y-m-d H:i:s')
		),'alert_date asc');

		$oLogs = Service::load('contractlog')->getContractLogs(array(
			'contract_id' => $iContractId
		),'create_date desc',20);
		$aLogs = $oLogs->results();
		unset($oLogs);
		$aLogs = array_reverse($aLogs);

		$aLogOwnerIds = array();
		foreach ($aLogs as $oLog) {
			$aLogOwnerIds[$oLog->member_id] = $oLog->member_id;
		}
		$aLogOwners = array();
		if (!empty($aLogs)) {
			$oLogOwners = Service::load('member')->getMembers(array('parent_id'=>$this->_iParentId,'member_id'=>$aLogOwnerIds));
			foreach ($oLogOwners as $oLogOwner) {
				$aLogOwners[$oLogOwner->member_id] = $oLogOwner;
			}
		}
		/*if ($this->_iMemberId == 1) {
			echo '<pre>'; var_dump($aLogOwners);
			return false;
		}*/

		$this->set('aLogOwners',$aLogOwners);

		$oTags = Service::load('contracttag')->getContractTags(array(
			'contract_id' => $iContractId
		),'create_date desc',10);

		$oCustomFieldValueTexts = Service::load('customfieldvaluetext')->getCustomFieldValueTexts(array(
			'parent_id'    => $this->_iParentId
			,'contract_id' => $iContractId
		));
		
		$oSupportDocs = Service::load('contractsupportdoc')->getContractSupportDocs(array(
		    'contract_id' => $oContract->contract_id
		));
		$this->set('oSupportDocs',$oSupportDocs);

		$this->set('oCustomFields',$oCustomFields);
		$this->set('oCustomFieldValueTexts',$oCustomFieldValueTexts);
		$this->set('oContract',$oContract);
		$this->set('aTeamMembers',$aTeamMembers);
		$this->set('oMembers',$oMembers);
		$this->set('oReminders',$oReminders);
		$this->set('aLogs',$aLogs);
		$this->set('oTags',$oTags);
		$this->set('sHeader','Edit Contract');
		$this->build('contracts/edit');
	}

	/**
	 * View a Contract
	 *
	 * @param int $iContractId
	 */
	public function view($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
		));

		$aTeamMemberIds = array($oContract->owner_id);
		foreach ($oTeamMembers as $oTeamMember) {
			$aTeamMemberIds[] = $oTeamMember->member_id;
		}

		$bCurrentUserCanEdit = false;
		$bCurrentUserCanView = false;
		if ($this->_iMemberId == $oContract->owner_id ||
			$this->_iMemberId == $oContract->parent_id) {
			$bCurrentUserCanEdit = true;
			$bCurrentUserCanView = true;
		}
		$oMembers = new ServiceResponse();
		$aTeamMembers = array();
		if (!empty($aTeamMemberIds)) {
			$oMembers = Service::load('member')->getMembers(array(
				'member_id'  => $aTeamMemberIds
			));

			$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccountsWithMemberAccountData(array(
				'other_member_accounts.parent_id' => $this->_iParentId
				,'other_member_accounts.member_id' => $aTeamMemberIds
			));
			//echo '<pre>'; var_dump($aTeamMemberIds,$oOtherMemberAccounts); return true;

			$aUsers = array_merge($oMembers->getResults(),$oOtherMemberAccounts->getResults());

			foreach ($aUsers as $oMember) {
				if ($oContract->owner_id == $oMember->member_id) {
					$oMember->level = 'Owner';
				} else {
					foreach ($oTeamMembers as $oTeamMember) {
						if ($oTeamMember->member_id == $oMember->member_id) {
							$oMember->level = $oTeamMember->readable_level;
							if ($oTeamMember->member_id == $this->_iMemberId) {
								$bCurrentUserCanView = true;
								if ($oTeamMember->level == ContractMemberModel::LEVEL_EDITOR) {
									$bCurrentUserCanEdit = true;
								}
							}
						}
					}
				}
				$aTeamMembers[$oMember->member_id] = $oMember;
			}
		}

		$oApprovalStepPendings = Service::load('contractapproval')->getContractApprovals(array(
			'contract_id' => $oContract->contract_id
			,'member_id'  => $this->_iMemberId
			,'status'     => ContractApprovalModel::STATUS_PENDING
		));

		if (!empty($_GET['debug_show'])) {
			echo '<pre>'; var_dump($this->_iMemberId,$bCurrentUserCanEdit,$aTeamMembers);
			return true;
		}

		//if (!$bCurrentUserCanView && !$oApprovalStepPendings->count) {
		if (!$bCurrentUserCanView) {
			$this->session->error('You do not have access to this contract');
			redirect('contracts');
		}

		$oReminders = Service::load('reminder')->getReminders(array(
			'contract_id' => $iContractId
			,'status'     => ReminderModel::STATUS_ACTIVE
		),'alert_date asc');

		$oLogs = Service::load('contractlog')->getContractLogs(array(
			'contract_id' => $iContractId
		),'create_date desc',20);
		$aLogs = $oLogs->results();
		unset($oLogs);
		$aLogs = array_reverse($aLogs);

		$aLogOwnerIds = array();
		foreach ($aLogs as $oLog) {
			$aLogOwnerIds[$oLog->member_id] = $oLog->member_id;
		}
		$aLogOwners = array();
		if (!empty($aLogs)) {
			$oLogOwners = Service::load('member')->getMembers(array('member_id'=>$aLogOwnerIds));
			foreach ($oLogOwners as $oLogOwner) {
				$aLogOwners[$oLogOwner->member_id] = $oLogOwner;
			}
		}

		$this->set('aLogOwners',$aLogOwners);

		$aApprovalSteps = Service::load('contractapproval')->getContractApprovalsWithAssignees($oContract->contract_id)->getResults();

		$aApprovalStepsSorted = array();
		$bCurrentUserPendingApproval = false;
		$iCurrentUserPendingApprovalId = null;
		foreach ($aApprovalSteps as $oApprovalStep) {
			if (!isset($aApprovalStepsSorted[$oApprovalStep->step]['status'])) {
				$aApprovalStepsSorted[$oApprovalStep->step]['status'] = ContractApprovalModel::STATUS_PENDING;
			}

			$aApprovalStepsSorted[$oApprovalStep->step]['steps'][] = $oApprovalStep;
			$aApprovalStepsSorted[$oApprovalStep->step]['type'] = $oApprovalStep->type;

			if ($oApprovalStep->status == ContractApprovalModel::STATUS_PENDING &&
				$oApprovalStep->member_id == $this->_iMemberId) {
				$bCurrentUserPendingApproval = true;
				$iCurrentUserPendingApprovalId = $oApprovalStep->contract_approval_id;
			}

			if ($oApprovalStep->status != ContractApprovalModel::STATUS_SKIPPED &&
				$oApprovalStep->status > $aApprovalStepsSorted[$oApprovalStep->step]['status']) {
				$aApprovalStepsSorted[$oApprovalStep->step]['status'] = $oApprovalStep->status;
			}
		}

		$oCustomFields = Service::load('customfield')->getCustomFields(array('parent_id'=>$this->_iParentId));
		$oCustomFieldValueTexts = Service::load('customfieldvaluetext')->getCustomFieldValueTexts(array(
			'parent_id'    => $this->_iParentId
			,'contract_id' => $iContractId
		));
		$oCustomFieldValueCheckboxes = Service::load('customfieldvaluecheckbox')->getCustomFieldValueCheckboxes(array(
			'parent_id'    => $this->_iParentId
			,'contract_id' => $iContractId
		));

		$oRevisions = Service::load('contractrevision')->getFileRevisions(array('contract_id' => $iContractId),5);
		$this->set('oRevisions',$oRevisions);

		$this->set('oCustomFields',$oCustomFields);
		$this->set('oCustomFieldValueTexts',$oCustomFieldValueTexts);
		$this->set('oCustomFieldValueCheckboxes',$oCustomFieldValueCheckboxes);
		$this->set('aApprovalStepsSorted',$aApprovalStepsSorted);
		$this->set('bCurrentUserPendingApproval',$bCurrentUserPendingApproval);
		$this->set('iCurrentUserPendingApprovalId',$iCurrentUserPendingApprovalId);
		$this->set('bCurrentSubHasApprovalAccess',$this->_getSubscription()->approvals);

		$oTags = Service::load('contracttag')->getContractTags(array(
			'contract_id' => $iContractId
		),'create_date desc',10);

		$this->session->set_userdata('member_last_page','/contracts/view/'.$iContractId);

		//echo '<pre>'; var_dump($oContract);
		$oSignatures = Service::load('contractsignature')->getContractSignatures(array(
			'contract_id' => $iContractId
		));

		$aSignatureMembers = array();
		$aSignatureMembersToGet = array();
		foreach ($oSignatures as $oSignature) {
			if (!empty($aTeamMembers[$oSignature->member_id])) {
				$aSignatureMembers[$oSignature->member_id] = $aTeamMembers[$oSignature->member_id];
			} else {
				$aSignatureMembersToGet[$oSignature->member_id] = $oSignature->member_id;
			}
		}
		if (!empty($aSignatureMembersToGet)) {
			$oSignatureMembersToSort = Service::load('member')->getMembers(array('member_id'=>$aSignatureMembersToGet));
			foreach ($oSignatureMembersToSort as $oSignatureMemberToSort) {
				$aSignatureMembers[$oSignature->member_id] = $oSignatureMemberToSort;
			}
		}

		$oDocuSignContracts = Service::load('docusigncontract')->getDocusignContracts(array(
			'contract_id' => $iContractId
			,'status'     => array(
				DocusignContractModel::STATUS_PENDING
				,DocusignContractModel::STATUS_SENT_TO_DOCUSIGN
				,DocusignContractModel::STATUS_SENT_TO_SIGNERS
			)
		));
		$bContractSentToDocusign = false;
		foreach ($oDocuSignContracts as $oDocuSignContract) {
			if ($oDocuSignContract->status == DocusignContractModel::STATUS_SENT_TO_DOCUSIGN) {
				$bContractSentToDocusign = true;
			}
		}

		$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array(
			'parent_id' => $this->_iParentId
			,'status'   => DocusignAccessTokenModel::STATUS_ACTIVE
		))->reset();
		if (empty($oToken) && $oDocuSignContracts->count) {
			$this->session->current_error($this->lang->line('docusign_expired_token_notification'));
		} elseif ($oSignatures->count && $bContractSentToDocusign)
		{
			$this->session->current_info($this->lang->line('docusign_push_contract_page_notification'));
		}

		$this->set('bContractSentToDocusign',$bContractSentToDocusign);
		$this->set('oSignatures',$oSignatures);
		$this->set('aSignatureMembers',$aSignatureMembers);
		$this->set('oContract',$oContract);
		$this->set('aTeamMembers',$aTeamMembers);
		$this->set('aMembers',$aMembers);
		$this->set('oReminders',$oReminders);
		$this->set('aLogs',$aLogs);
		$this->set('oTags',$oTags);
		$this->set('bCurrentUserCanEdit',$bCurrentUserCanEdit);
		$this->set('sHeader','View Contract');

		$oSupportDocs = Service::load('contractsupportdoc')->getContractSupportDocs(array(
			'contract_id' => $oContract->contract_id
		));
		$this->set('oSupportDocs',$oSupportDocs);
		
		if ($oContract->board_id) {
		    $board = Service::load('board')->getBoards(array('board_id' => $oContract->board_id))->first();
		    $this->set('oBoard',$board);
		}

		if (1 == $this->_iMemberId) {
			$this->build('contracts/view_lessang');
		} else {
			$this->build('contracts/view_with_support_docs');
		}
	}

	public function add_support_doc($iContractId) {
		if (!can_access_feature('support_docs',$this->_iMemberId)) {
			redirect('contracts');
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
			,'member_id'  => $this->_iMemberId
		));

		if (empty($oTeamMember) &&
			$oContract->owner_id != $this->_iMemberId  &&
			$this->_iParentId != $this->_iMemberId
		) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oSupportDocs = Service::load('contractsupportdoc')->getContractSupportDocs(array(
			'contract_id' => $oContract->contract_id
		));

		if ($oSupportDocs->count >= $this->config->item('max_supporting_docs_per_contract')) {
			$this->session->error('Maximum number of support documents already uploaded for this contract.');
			redirect('contracts/view/'.$iContractId);
		}

		$this->set('oContract',$oContract);
		$this->load->view('contracts/add_supporting_doc',$this->aData);
	}

	public function upload_support_document_ajax($iContractId) {
		if (!can_access_feature('support_docs',$this->_iMemberId)) {
			echo json_encode(array(
				'success'     => 0
				,'error'      => 'You do not have access to upload support documents.'
				,'error_code' => 401
			));
			return true;
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			echo json_encode(array(
				'success'     => 0
				,'error'      => 'Contract not found.'
				,'error_code' => 404
			));
			return true;
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
			,'member_id'  => $this->_iMemberId
		));

		if (empty($oTeamMember) &&
			$oContract->owner_id != $this->_iMemberId  &&
			$this->_iParentId != $this->_iMemberId
		) {
			echo json_encode(array(
				'success'     => 0
				,'error'      => 'Contract not found.'
				,'error_code' => 401
			));
			return true;
		}

		$oSupportDocs = Service::load('contractsupportdoc')->getContractSupportDocs(array(
			'contract_id' => $oContract->contract_id
		));

		if ($oSupportDocs->count >= $this->config->item('max_supporting_docs_per_contract')) {
			echo json_encode(array(
				'success'     => 0
				,'error'      => 'Maximum number of support documents already uploaded for this contract.'
				,'error_code' => 401
			));
			return true;
		}

		if (empty($_FILES['contract_file']['size'])) {
			echo json_encode(array(
				'success'     => 0
				,'error'      => 'Support document not uploaded correctly or empty.'
				,'error_code' => 400
			));
			return true;
		}

		$oResponse = Service::load('contractsupportdoc')->addContractSupportDoc(new ContractSupportDocModel(array(
			'contract_id'   => $iContractId
			,'owner_id'     => $this->_iMemberId
			,'parent_id'    => $this->_iParentId
			,'file_name'    => $_FILES['contract_file']['name']
			,'enct'         => 1
			,'create_date'  => date('Y-m-d H:i:s')
			,'last_updated' => date('Y-m-d H:i:s')
		)),$_FILES['contract_file']['tmp_name']);

		if (!$oResponse->isOk()) {
			echo json_encode(array(
				'success'     => 0
				,'error'      => 'There was an error uploading the document.  Please contact support.'
				,'error_code' => 500
			));
			return false;
		}

		$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
			'member_id'    => $this->_iMemberId
			,'contract_id' => $iContractId
			,'message'     => 'uploaded a support document '.retud($_FILES['contract_file']['name']).' for '
			,'type'        => ContractLogModel::TYPE_UPDATE
			,'create_date' => date('Y-m-d H:i:s')
		)));

		$this->session->success('Support document added');
		echo json_encode(array(
			'success' => 1
		));
		return true;
	}

	public function upload() {
		//log_message('required','contract::upload');
		$oUsers = Service::load('member')->getMembers(array(
			'parent_id' => $this->_iParentId
			,'status'   => array(MemberModel::StatusActive,MemberModel::StatusPending)
		));

		$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccountsWithMemberAccountData(array(
			'other_member_accounts.parent_id' => $this->_iParentId
		));

		$aUsers = array_merge($oUsers->getResults(),$oOtherMemberAccounts->getResults());

		if (!empty($_GET['show_debug'])) {
			echo '<pre>'; var_dump($this->_iMemberId,$oUsers);
			return true;
		}

		$oSub = $this->_getSubscription();
		if (!$oSub->isActive()) {
			$this->session->error('Your subscription is no longer active.  Please upgrade to continue.');
			redirect('welcome');
		}

		$iContractCount = Service::load('contract')->getContractCount(array(
			'parent_id' => $this->_iParentId,
			'status !=' => ContractModel::STATUS_DELETED
		))->total;
		$oSub = $this->_getSubscription();
		if ($iContractCount >= $oSub->contract_limit) {
			$this->session->error('Contract limit reached.');
			redirect('welcome');
		}

		$oBoards = Service::load('board')->getBoards(array('parent_id'=>$this->_iParentId, 'parent_board_id IS NULL' => null), 'name asc');

		$oOwner = Service::load('member')->getMember(array(
			'member_id' => $this->_iMemberId
		))->reset();

		$this->set('bCurrentSubHasApprovalAccess',$this->_getSubscription()->approvals);
		$this->set('oBoards',$oBoards);
		$this->set('aUsers',$aUsers);
		$this->set('oOwner',$oOwner);

		if (can_access_feature('docusign',$this->_iMemberId)) {
			//$this->load->view('contracts/upload_signatures',$this->aData);
			$this->load->view('contracts/upload_addfoldersusers',$this->aData);
		} else {
			$this->load->view('contracts/upload_approvals',$this->aData);
		}
	}

	public function upload_file_version($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oDocuSignContracts = Service::load('docusigncontract')->getDocusignContracts(array(
			'contract_id' => $iContractId
			,'status'     => array(
				DocusignContractModel::STATUS_SENT_TO_DOCUSIGN
				,DocusignContractModel::STATUS_SENT_TO_SIGNERS
			)
		));
		if ($oDocuSignContracts->count) {
			$this->session->error('You can not update document while document is in DocuSign.');
			redirect('contracts/view/'.$iContractId);
		}

		$this->set('oContract',$oContract);
		if (can_access_feature('new_upload_version',$this->_iMemberId)) {
			$this->load->view('contracts/upload_file_versionv2',$this->aData);
		} else {
			$this->load->view('contracts/upload_file_version',$this->aData);
		}
	}

	public function upload_file_version_ajax($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			echo json_encode(array('success'=>0,'error'=>'Contract not found.'));
			return false;
		}

		$oContract->last_updated = convert_utc_datetime(date('Y-m-d H:i:s'), $this->cTimeZone);
		if (!empty($_FILES['contract_file']['size'])) {
			$oContract->file_name = $_FILES['contract_file']['name'];
			$oUpdate = Service::load('contract')->updateContract($oContract, $_FILES['contract_file']['tmp_name']);

			if ($oUpdate->isOk()) {
				$oContract = $oUpdate->reset();
				$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'member_id'    => $this->_iMemberId
					,'contract_id' => $iContractId
					,'message'     => 'updated contract'
					,'type'        => ContractLogModel::TYPE_UPDATE
					//,'create_date' => date('Y-m-d H:i:s')
				    ,'create_date' => convert_utc_datetime(date('Y-m-d H:i:s'), $this->cTimeZone)
				)));

				Service::load('contractrevision')->generateRevision($oContract);

				echo json_encode(array('success'=>1,'error'=>''));
				return false;

			} else {
				echo json_encode(array('success'=>0,'error'=>'Unable to update contract.'));
				return false;
			}
		} else {
			echo json_encode(array('success'=>0,'error'=>'No file found.'));
			return false;
		}

		echo json_encode(array('success'=>0,'error'=>'Unknown Error.'));
		return false;
	}

	public function bulk() {
		if (!can_access_feature('new_upload_version',$this->_iMemberId)) {
			redirect('contracts/upload');
		}

		$oSub = $this->_getSubscription();
		$iContractCount = Service::load('contract')->getContractCount(array(
			'parent_id' => $this->_iParentId,
			'status !=' => ContractModel::STATUS_DELETED
		))->total;

		$iCountRemaining = $oSub->contract_limit - $iContractCount;
		if ($iCountRemaining <= 0) {
			$this->session->error('Contract limit reached.');
			redirect('contracts');
		}

		$this->set('iCountRemaining',$iCountRemaining);
		$this->load->view('contracts/bulk',$this->aData);
	}

	public function upload_bulk_ajax() {
		if (!can_access_feature('new_upload_version',$this->_iMemberId)) {
			echo json_encode(array('success'=>0,'error'=>'Access denied.'));
			return false;
		}

		//log_message('required','::upload_ajax '.print_r($_FILES,true));
		if (empty($_FILES['contract_file']['name'])) {
			echo json_encode(array('success'=>0,'error'=>'No files submitted.'));
			return false;
		}

		$oSub = $this->_getSubscription();
		$iContractCount = Service::load('contract')->getContractCount(array(
			'parent_id' => $this->_iParentId,
			'status !=' => ContractModel::STATUS_DELETED
		))->total;

		$iCountRemaining = $oSub->contract_limit - $iContractCount;
		if ($iContractCount <= 0) {
			echo json_encode(array('success'=>0,'error'=>'Contract Limit Reached'));
			return false;
		}

		$oCS = Service::load('contract');
		$oCRS = Service::load('contractrevision');
		foreach ($_FILES['contract_file']['name'] as $iIndex=>$sFileName) {
			$aFile = array(
				'name'      => $sFileName
				,'type'     => $_FILES['contract_file']['type'][$iIndex]
				,'location' => $_FILES['contract_file']['tmp_name'][$iIndex]
			);

			$oAdded = $oCS->addContract(new ContractModel(array(
				'owner_id'      => $this->_iMemberId
				,'parent_id'    => $this->_iParentId
				,'name'         => $sFileName
				,'company'      => ''
				,'start_date'   => null
				,'end_date'     => null
				,'value'        => null
				,'type'         => ContractModel::TYPE_BUY_SIDE
				,'status'       => ContractModel::STATUS_ACTIVE
				,'enct'         => 1
				,'file_name'    => $sFileName
				,'create_date'  => date('Y-m-d H:i:s')
				,'last_updated' => date('Y-m-d H:i:s')
			)),$_FILES['contract_file']['tmp_name'][$iIndex]);

			if ($oAdded->isOk()) {
				$oContract = $oAdded->first();
				$oCRS->generateRevision($oContract);

				$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'member_id'    => $this->_iMemberId
					,'contract_id' => $oContract->contract_id
					,'message'     => 'added contract'
					,'type'        => ContractLogModel::TYPE_UPDATE
					,'create_date' => date('Y-m-d H:i:s')
				)));
				
				send_analytic_event('Contract Created', null, ['contractId' => $oContract->contract_id, 'contractName' => $oContract->name]);
			}
		}

		echo json_encode(array('success'=>1));
	}

	public function upload_ajax() {
		//log_message('required','::upload_ajax '.print_r($_FILES,true));
		if (empty($_FILES['contract_file']['name'])) {
			echo json_encode(array('success'=>0,'error'=>'No files submitted.'));
			return false;
		}

		$oSub = $this->_getSubscription();
		$iContractCount = Service::load('contract')->getContractCount(array(
			'parent_id' => $this->_iParentId,
			'status !=' => ContractModel::STATUS_DELETED
		))->total;

		$iCountRemaining = $oSub->contract_limit - $iContractCount;
		log_message('required',"member: {$oSub->member_id} limit: {$oSub->contract_limit} count: {$iContractCount}");
		if ($iCountRemaining <= 0) {
			echo json_encode(array('success'=>0,'error'=>'Contract Limit Reached'));
			return false;
		}

		$oCS = Service::load('contract');
		$oCRS = Service::load('contractrevision');
		$aNewContractIds = $this->session->userdata('uploaded_contract_ids',array());
		foreach ($_FILES['contract_file']['name'] as $iIndex=>$sFileName) {
			$aFile = array(
				'name'      => $sFileName
				,'type'     => $_FILES['contract_file']['type'][$iIndex]
				,'location' => $_FILES['contract_file']['tmp_name'][$iIndex]
			);

			$oAdded = $oCS->addContract(new ContractModel(array(
				'owner_id'      => $this->_iMemberId
				,'parent_id'    => $this->_iParentId
				,'name'         => $sFileName
				,'company'      => ''
				,'start_date'   => null
				,'end_date'     => null
				,'value'        => null
				,'type'         => ContractModel::TYPE_BUY_SIDE
				,'status'       => ContractModel::STATUS_ACTIVE
				,'enct'         => 1
				,'file_name'    => $sFileName
				,'create_date'  => date('Y-m-d H:i:s')
				,'last_updated' => date('Y-m-d H:i:s')
			)),$_FILES['contract_file']['tmp_name'][$iIndex]);

			if ($oAdded->isOk()) {
				$oContract = $oAdded->first();
				$oCRS->generateRevision($oContract);
				$aNewContractIds[] = $oContract->contract_id;

				$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'member_id'    => $this->_iMemberId
					,'contract_id' => $oContract->contract_id
					,'message'     => 'added contract'
					,'type'        => ContractLogModel::TYPE_UPDATE
					,'create_date' => date('Y-m-d H:i:s')
				)));
				
				send_analytic_event('Contract Created', null, ['contractId' => $oContract->contract_id, 'contractName' => $oContract->name]);
			}
		}
		$this->session->set_userdata('uploaded_contract_ids',$aNewContractIds);

		echo json_encode(array('success'=>1));
	}

	public function ajax_finish_upload() {
		//echo json_encode(array('success'=>0,'error'=>'testing stand by.'));
		//return false;

		$aNewContractIds = $this->session->userdata('uploaded_contract_ids',array());

		if (empty($aNewContractIds)) {
			//log_message('required','contract::ajax_finish_upload uploaded_contract_ids empty');
			echo json_encode(array('success'=>1));
			return true;
		}

		//log_message('required',$this->_iMemberId.' contract::ajax_finish_upload $aNewContractIds '.print_r($aNewContractIds,true));
		//log_message('required',$this->_iMemberId.' contract::ajax_finish_upload post '.print_r($_POST,true));
		$mBoard = (!empty($_POST['board']))?trim($_POST['board']):'';
		$aUserData = (!empty($_POST['users']))?$_POST['users']:array();

		$aNewUsers = array();
		$aCleanedUsers = array();
		$aRequestedUserIds = array();
		$aActualUserIds = array($this->_iMemberId);

		if (!empty($mBoard)) {
			if (is_numeric($mBoard)) {
				$oBoard = Service::load('board')->getBoards(array('board_id'=>$mBoard,'parent_id'=>$this->_iParentId))->first();
				if (empty($oBoard)) {
					echo json_encode(array('success'=>0,'error'=>'Folder not found.'));
					return false;
				}
				Service::load('contract')->updateContracts(array('contract_id'=>$aNewContractIds),array('board_id'=>$oBoard->board_id));
			} else {
				$this->load->library('form_validation');
				$this->form_validation->set_rules($this->board_validation);
				$this->form_validation->set_data(array('name'=>$mBoard));
				if ($this->form_validation->run()) {
					// create board
					$oBoard = new BoardModel(array(
						'parent_id'    => $this->_iParentId
						,'name'        => $mBoard
						,'create_date' => date('Y-m-d H:i:s')
					));

					$oAdd = Service::load('board')->addBoard($oBoard);

					if (!$oAdd->isOk()) {
						echo json_encode(array('success'=>0,'error'=>'We were unable to add the new folder.  Please contact support.'));
						return false;
					}
					$oBoard = $oAdd->reset();
					Service::load('contract')->updateContracts(array('contract_id'=>$aNewContractIds),array('board_id'=>$oBoard->board_id));

				} else {
					echo json_encode(array('success'=>0,'error'=>$this->form_validation->first_error()));
					return false;
				}
			}
		}

		if (!empty($aUserData)) {
			$oCMS = Service::load('contractmember');

			list($aRequestedUserIds,$aCleanedUsers,$aNewUsers) = $this->_cleanPostedUserData($aUserData);
			//log_message('required','ajax_finish_upload:'._LINE__.' $aCleanedUsers -> '.print_r($aCleanedUsers,true));

			if (!empty($aRequestedUserIds)) {
				//log_message('required','contract::ajax_finish_upload userids: '.print_r($aRequestedUserIds,true));

				$oUsers = Service::load('member')->getMembers(array('member_id' => $aRequestedUserIds, 'parent_id' => $this->_iParentId));

				$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccount(array(
					'parent_id' => $this->_iParentId,
					'member_id' => $aRequestedUserIds
				));

				$aUsers = array_merge($oUsers->getResults(),$oOtherMemberAccounts->getResults());

				if (!empty($aUsers)) {
					foreach ($aUsers as $oUser) {
						$aActualUserIds[] = $oUser->member_id;
					}

					foreach ($aCleanedUsers as $iMemberId=>$iLevel) {
						if (!in_array($iMemberId,$aActualUserIds)) {
							unset($aCleanedUsers[$iMemberId]);
						}
					}

					if (!empty($aCleanedUsers)) {
						foreach ($aNewContractIds as $iContractId) {
							foreach ($aCleanedUsers as $iUserId => $iLevel) {
								$oCMS->addContractMember(new ContractMemberModel(array(
									'contract_id'   => $iContractId
									, 'member_id'   => $iUserId
									, 'level'       => $iLevel
									, 'create_date' => date('Y-m-d H:i:s')
								)));
							}
						}
					}
				} else {
					//log_message('required','contract::ajax_finish_upload no user in db');
				}
			} else {
				//log_message('required','contract::ajax_finish_upload no userids');
			}
		}

		if (!empty($_POST['workflow'])) {
			Service::load('contractapproval');

			$iStepId = 1;
			$iStepStatus = ContractApprovalModel::STATUS_PENDING;

			foreach ($_POST['workflow'] as $aStep) {
				if (empty($aStep['type']) || empty($aStep['members'])) {
					continue;
				}

				$iStepType = ContractApprovalModel::TYPE_ALL;
				if (strcasecmp('Require Any',$aStep['type']) === 0) {
					$iStepType = ContractApprovalModel::TYPE_ANY;
				}

				foreach ($aNewContractIds as $iContractId) {
					foreach ($aStep['members'] as $mStepMember) {
						if (is_numeric($mStepMember)) {
							$iStepMemberId = $mStepMember;
						} elseif ($aNewUsers[$mStepMember]) {
							$iStepMemberId = $aNewUsers[$mStepMember];
						} else {
							continue;
						}

						if (!in_array($iStepMemberId,$aActualUserIds)) {
							continue;
						}

						Service::load('contractapproval')->addContractApproval(new ContractApprovalModel(array(
							'contract_id'  => $iContractId
							,'member_id'   => $iStepMemberId
							,'step'        => $iStepId
							,'type'        => $iStepType
							,'status'      => $iStepStatus
							,'create_date' => date('Y-m-d H:i:s')
						)));
					}

					if ($iStepId == 1) {
						$oMemberToNotify = Service::load('member')->getMember(array('member_id'=>$iStepMemberId))->reset();
						$oContract = Service::load('contract')->getContracts(array(
							'contract_id'  => $iContractId
							,'parent_id'   => $this->_iParentId
						))->first();

						if (!empty($oMemberToNotify)) {
							$this->_sendApprovalNotification($oMemberToNotify,$oContract);
						}
					}
				}

				$iStepId++;
				$iStepStatus = ContractApprovalModel::STATUS_WAITING;
			}
		}

		if (!empty($_POST['signatures'])) {
			$bSigsAdded = false;
			foreach ($_POST['signatures'] as $mSignatory) {
				if (is_numeric($mSignatory)) {
					$iSignatory = $mSignatory;
				} elseif ($aNewUsers[$mSignatory]) {
					$iSignatory = $aNewUsers[$mSignatory];
				} else {
					//log_message('error','ajax_finish_upload:'.__LINE__.' not found: '.$mSignatory);
					continue;
				}
				//log_message('error','ajax_finish_upload:'.__LINE__.' found: '.$mSignatory.' '.$iSignatory);

				if (!in_array($iSignatory,$aActualUserIds)) {
					continue;
				}

				foreach ($aNewContractIds as $iContractId) {
					Service::load('contractsignature')->addContractSignature(new ContractSignatureModel(array(
						'contract_id'  => $iContractId
						,'member_id'   => $iSignatory
						,'create_date' => date('Y-m-d H:i:s')
					)));
				}
				
				$oContract = Service::load('contract')->getContracts(array('contract_id' => $iContractId))->first();
				if ($oContract && $oContract->docusign_error == ContractModel::DOCUSIGN_ERROR_CREATE) {
				    $oContract->docusign_error == 0;
				    Service::load('contract')->updateContract($oContract);
				}
				
				$bSigsAdded = true;
			}

			if (empty($bSigsAdded)) {
				$this->session->success('Your document(s) will be sent to DocuSign shortly.');

				foreach ($aNewContractIds as $iContractId) {
					Service::load('docusigncontract')->addDocusignContract(new DocusignContractModel(array(
						'contract_id'  => $iContractId
						,'status'      => DocusignContractModel::STATUS_PENDING
						,'create_date' => date('Y-m-d H:i:s')
					)));
				}
			}
		}

		$this->session->unset_userdata('uploaded_contract_ids');

		if (count($aNewContractIds) == 1) {
			echo json_encode(array('success'=>1,'contract_id'=>array_shift($aNewContractIds)));
			return true;
		}

		echo json_encode(array('success'=>1));
	}

	public function ajax_flush_new_contracts_in_session() {
		$this->session->unset_userdata('uploaded_contract_ids');
		echo json_encode(array('success'=>1));
	}

	public function add_log($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
		));

		$aTeamMemberIds = array($oContract->owner_id);
		foreach ($oTeamMembers as $oTeamMember) {
			$aTeamMemberIds[] = $oTeamMember->member_id;
		}

		$bCurrentUserCanEdit = false;
		if ($this->_iMemberId == $oContract->owner_id ||
			$this->_iMemberId == $oContract->parent_id) {
			$bCurrentUserCanEdit = true;
			$bCurrentUserCanView = true;
		}
		$oMembers = new ServiceResponse();
		$aTeamMembers = array();
		if (!empty($aTeamMemberIds)) {
			$oMembers = Service::load('member')->getMembers(array(
				'member_id'  => $aTeamMemberIds
			));
			foreach ($oMembers as $oMember) {
				if ($oContract->owner_id == $oMember->member_id) {
					$oMember->level = 'Owner';
				} else {
					foreach ($oTeamMembers as $oTeamMember) {
						if ($oTeamMember->member_id == $oMember->member_id) {
							$oMember->level = $oTeamMember->readable_level;
							if ($oTeamMember->member_id == $this->_iMemberId) {
								$bCurrentUserCanView = true;
								if ($oTeamMember->level == ContractMemberModel::LEVEL_EDITOR) {
									$bCurrentUserCanEdit = true;
								}
							}
						}
					}
				}
				$aTeamMembers[$oMember->member_id] = $oMember;
			}
		}

		if (!$bCurrentUserCanView) {
			$this->session->error('You do not have access to this contract.');
			redirect('contracts');
		}

		$this->form_validation->set_rules($this->contract_log_validation);
		if (!$this->form_validation->run()) {
			$this->session->error($this->form_validation->first_error());
			redirect('/contracts/view/'.$iContractId);
		}

		$oAdd = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
			'member_id'    => $this->_iMemberId
			,'contract_id' => $iContractId
			,'message'     => set_value('message')
			,'type'        => ContractLogModel::TYPE_NOTE
			,'create_date' => date('Y-m-d H:i:s')
		)));

		redirect('/contracts/view/'.$iContractId);
	}

	public function browse_notifications() {
		$this->load->view('contracts/browse_notifications',$this->aData);
	}

	public function delete_contract_support_doc($iContractSupportDocId) {
		if (!can_access_feature('support_docs',$this->_iMemberId)) {
			redirect('contracts');
		}

		$oSupportDoc = Service::load('contractsupportdoc')->getContractSupportDocs(array(
			'contract_support_doc_id' => $iContractSupportDocId
		))->reset();

		if (empty($oSupportDoc)) {
			$this->session->error('Contract support document not found.');
			redirect('contracts');
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $oSupportDoc->contract_id
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
			,'member_id'  => $this->_iMemberId
		));

		if (empty($oTeamMember) &&
			$oContract->owner_id != $this->_iMemberId  &&
			$this->_iParentId != $this->_iMemberId &&
			$this->iMemberId != $oSupportDoc->owner_id
		) {
			$this->session->error('You are not authorized to delete this contract support document.');
			redirect('contracts/view/'.$oSupportDoc->contract_id);
		}

		if (!empty($_GET['cdc'])) {
			$oResponse = Service::load('contractsupportdoc')->deleteContractSupportDocs(array(
				'contract_support_doc_id' => $iContractSupportDocId
			));

			if ($oResponse->isOk()) {
				$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'member_id'    => $this->_iMemberId
					,'contract_id' => $oContract->contract_id
					,'message'     => 'deleted a support document '.retud($oSupportDoc->file_hash).' for '
					,'type'        => ContractLogModel::TYPE_UPDATE
					,'create_date' => date('Y-m-d H:i:s')
				)));

				$this->session->success('Support document deleted.');
				redirect('contracts/view/'.$oSupportDoc->contract_id);
			}

			$this->session->error('Unable to delete support document.');
		}

		$this->set('oContract',$oContract);
		$this->set('oSupportDoc',$oSupportDoc);
		$this->load->view('contracts/delete_contract_support_doc',$this->aData);
	}

	public function delete($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		if ($oContract->_iParentId != $this->_iMemberId && $oContract->owner_id != $this->_iMemberId) {
			log_message('required','no delete: '.$oContract->_iParentId.' '.$oContract->owner_id.' '.$this->_iMemberId);
			$this->session->error('You are do not have permissions to delete this contract.  Please contact the document owner.');
			redirect('contracts');
		}

		//log_message('required','delete get params: '.print_r($_GET,true));
		if (!empty($_GET['cdc'])) {
			$oContract->status = ContractModel::STATUS_DELETED;
			$oContract->last_updated = date('Y-m-d H:i:s');
			Service::load('contract')->updateContract($oContract);

			$oReminders = Service::load('reminder')->getReminders(array('contract_id'=>$iContractId));
			$aReminderIds = array();
			foreach ($oReminders as $oReminder) {
				$aReminderIds[] = $oReminder->reminder_id;
			}
			if (!empty($aReminderIds)) {
				Service::load('remindermember')->deleteReminderMembers(array('reminder_id'=>$aReminderIds));
			}

			Service::load('reminder')->deleteReminders(array('contract_id'=>$iContractId));
			Service::load('contractapproval')->deleteContractApprovals(array('contract_id'=>$iContractId));
			
			send_analytic_event('Contract Deleted', null, ['contractId' => $oContract->contract_id, 'contractName' => $oContract->name]);
			
			$this->session->success('Deleted contract.');
			redirect('contracts');
		}

		$this->set('oContract',$oContract);
		$this->load->view('contracts/delete',$this->aData);
	}

	public function remove_access($iContractId,$iMemberId) {
		if ($this->_iParentId != $this->_iMemberId) {
			$this->session->error('Contract not found. EC:CNO');
			$this->_redirectLast('contracts');
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->reset();

		if (empty($oContract)) {
			$this->session->error('Contract not found. EC:CNF');
			$this->_redirectLast('contracts');
		}

		Service::load('contractmember')->deleteContractMembers(array(
			'contract_id' => $iContractId
			,'member_id'  => $iMemberId
		));

		$this->session->success('Access removed.');
		$this->_redirectLast('contracts');
	}

	public function change_access($iContractId,$iMemberId,$iType=0) {
		if ($this->_iParentId != $this->_iMemberId) {
			$this->session->error('Contract not found. EC:CNO');
			$this->_redirectLast('contracts');
		}

		if (!in_array($iType,array(0,1))) {
			$this->session->error('Could not update access. EC:IPR');
			$this->_redirectLast('contracts');
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->reset();

		if (empty($oContract)) {
			$this->session->error('Contract not found. EC:CNF');
			$this->_redirectLast('contracts');
		}

		$oMember = Service::load('member')->getMembers(array(
			'member_id'  => $iMemberId
			,'parent_id' => $this->_iParentId
		))->reset();

		if (empty($oMember)) {
			$this->session->error('Contract not found. EC:CNO');
			$this->_redirectLast('contracts');
		}

		$oTeamMember = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
			,'member_id'  => $iMemberId
		))->reset();

		if (empty($oTeamMember)) {
			Service::load('contractmember')->addContractMember(new ContractMember(array(
				'contract_id'  => $iContractId
				,'member_id'   => $iMemberId
				,'level'       => $iType
				,'create_date' => date('Y-m-d H:i:s')
			)));
			$this->session->success('Access updated.');
			$this->_redirectLast('contracts');
		}

		$oTeamMember->level = $iType;
		Service::load('contractmember')->updateContractMember($oTeamMember);
		$this->session->success('Access updated.');
		$this->_redirectLast('contracts');
	}

	public function transfer_to($iContractId,$iMemberId) {
		if ($this->_iParentId != $this->_iMemberId) {
			$this->session->error('Contract not found. EC:CNO');
			$this->_redirectLast('contracts');
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		$oMember = Service::load('member')->getMembers(array(
			'member_id'  => $iMemberId
			,'parent_id' => $this->_iParentId
		))->first();

		if (empty($oMember)) {
			$this->session->error('Contract not found. EC:CNO');
			$this->_redirectLast('contracts');
		}

		Service::load('contractmember')->deleteContractMembers(array(
			'contract_id' => $iContractId
			,'member_id'  => $oMember->member_id
		));

		$oContract->owner_id = $oMember->member_id;
		Service::load('contract')->updateContract($oContract);
		$this->session->success('Contract transferred.');
		$this->_redirectLast('contracts');
	}

	public function transfer($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		$this->set('sLastPage',$this->_getLastPage());

		if (empty($oContract)) {
			$this->session->error('Contract not found. EC:CMF');
			$this->_redirectLast('contracts');
		}

		if ($oContract->owner_id != $this->_iMemberId && $this->_iParentId != $this->_iMemberId) {
			$this->session->error('Contract not found. EC:CNO');
			$this->_redirectLast('contracts');
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
		));

		$aTeamMemberIds = array($oContract->owner_id);
		foreach ($oTeamMembers as $oTeamMember) {
			$aTeamMemberIds[] = $oTeamMember->member_id;
		}

		$aTeamMembers = array();
		if (!empty($aTeamMemberIds)) {
			$oMembers = Service::load('member')->getMembers(array(
				'member_id'  => $aTeamMemberIds
				,'parent_id' => $this->_iParentId
			));
			foreach ($oMembers as $oMember) {
				if ($oContract->owner_id == $oMember->member_id) {
					$oMember->level = 'Owner';
				} else {
					foreach ($oTeamMembers as $oTeamMember) {
						if ($oTeamMember->member_id == $oMember->member_id) {
							$oMember->level = $oTeamMember->readable_level;
						}
					}
				}
				$aTeamMembers[$oMember->member_id] = $oMember;
			}
		}

		$oAccountMembers = Service::load('member')->getMembers(array('parent_id'=>$this->_iParentId));

		if ($this->_isPost()) {
			$sNewOwnerEmail = $this->input->my_post('new_owner_email');
			if (!empty($sNewOwnerEmail)) {
				$sNewOwnerEmail = strtolower(trim($sNewOwnerEmail));
				foreach ($oAccountMembers as $oMember) {
					if (strcmp($oMember->email,$sNewOwnerEmail)===0) {
						if (in_array($oMember->member_id,$aTeamMemberIds)) {
							Service::load('contractmember')->deleteContractMembers(array(
								'contract_id' => $iContractId
								,'member_id'  => $oMember->member_id
							));
						}

						$oContract->owner_id = $oMember->member_id;
						Service::load('contract')->updateContract($oContract);
						$this->session->success('Contract transferred.');
						$this->_redirectLast('contracts');
					}
				}

				$this->session->current_error('User not found.');
			} else {
				$this->session->current_error('User email required.');
			}
		}

		$this->set('oContract',$oContract);
		$this->set('aTeamMembers',$aTeamMembers);
		$this->set('oAccountMembers',$oAccountMembers);
		$this->load->view('contracts/transfer',$this->aData);
	}

	protected function _cleanPostedUserData($aUserData) {
		$oCMS = Service::load('contractmember');
		$aNewUsers = array();
		$aCleanedUsers = array();
		$aRequestedUserIds = array();
		$oParent = Service::load('member')->getMembers(array('member_id' => $this->_iParentId))->first();
		$oInviter = Service::load('member')->getMembers(array('member_id' => $this->_iMemberId))->first();

		//log_message('required','_cleanPostedUserData:'._LINE__.' $aUseData -> '.print_r($aUseData,true));
		foreach ($aUserData as $aUser) {
			$aUser[1] = strtolower(str_replace(' ', '', $aUser[1]));
			//log_message('required','_cleanPostedUserData:'._LINE__.' running -> '.$aUser[0]);

			if (empty($aUser[1]) || !in_array($aUser[1], array('viewonly', 'editor'))) {
				//log_message('error','_cleanPostedUserData:'._LINE__.' invalid level -> '.$aUser[1]);
				continue;
			}

			if (is_numeric($aUser[0])) {
				$aRequestedUserIds[] = $aUser[0];
				//log_message('required','_cleanPostedUserData:'._LINE__.' is numeric -> '.$aUser[0]);
			} elseif (filter_var($aUser[0],FILTER_VALIDATE_EMAIL)) {
				//log_message('required','_cleanPostedUserData:'._LINE__.' to add -> '.$aUser[0]);
				// add user
				$oMember = Service::load('member')->getMembers(array('email' => $aUser[0]))->first();
				if (!empty($oMember)) {
					if ($oMember->parent_id != $this->_iParentId) {
						$oOtherMemberAccount = Service::load('othermemberaccount')->getOtherMemberAccount(array(
							'member_id' => $oMember->member_id
							,'parent_id' => $this->_iParentId
						))->first();
						if (empty($oOtherMemberAccount)) {
							// add as other member
							$oAdd = Service::load('othermemberaccount')->addOtherMemberAccount(new OtherMemberAccountModel(array(
								'member_id' => $oMember->member_id
								,'parent_id' => $this->_iParentId
								,'create_date' => date('Y-m-d H:i:s')
							)));
							if (!$oAdd->isOk()) {
								echo json_encode(array('success'=>0,'error'=>'We were unable to add a new user to your account.  Please contact support.'));
								return false;
							}
							Service::load('member')->sendSubaccountAddEmail($oMember,$oInviter,$oParent);
							$oMember = $oAdd->reset();
							$aNewUsers[$aUser[0]] = $oMember->member_id;
							$aUser[0] = $oMember->member_id;
							$aRequestedUserIds[] = $aUser[0];
						} else {
							$aNewUsers[$aUser[0]] = $oMember->member_id;
							$aUser[0] = $oMember->member_id;
							$aRequestedUserIds[] = $aUser[0];
						}
					} else {
						// they are already in this account, how did this happen?
						//log_message('required','_cleanPostedUserData:'._LINE__.' fail -> '.$aUser[0]);
						continue;
					}
				} else {
					// add user
					$oMember = Service::load('member')->addMember(new MemberModel(array(
						'email'        => $aUser[0]
						,'parent_id'   => $this->_iParentId
						,'password'    => md5($sEmail.md5($sEmail.'temppasswordSalt3dHA3h'.time().$this->_iParentId).'estraSalthe@#$%@#!$%^@$#%@'.time())
						,'create_date' => date('Y-m-d H:i:s')
					)))->reset();
					if (empty($oMember)) {
						echo json_encode(array('success'=>0,'error'=>'We were unable to add a new user to your account.  Please contact support.'));
						return false;
					}
					Service::load('member')->sendSubaccountConfirmationEmail($oMember);
					//log_message('required','_cleanPostedUserData:'._LINE__.' New $oMember -> '.print_r($oMember,true));
					$aNewUsers[$aUser[0]] = $oMember->member_id;
					$aUser[0] = $oMember->member_id;
					$aRequestedUserIds[] = $aUser[0];
				}
			} else {
				//log_message('error','_cleanPostedUserData:'._LINE__.' invalid user -> '.print_r($aUser[0],true));
				continue;
			}

			$aCleanedUsers[$aUser[0]] = (strcmp($aUser[1], 'viewonly') === 0) ? ContractMemberModel::LEVEL_VIEW_ONLY : ContractMemberModel::LEVEL_EDITOR;
		}

		return array($aRequestedUserIds,$aCleanedUsers,$aNewUsers);
	}

	public function ajax_update_team($iContractId) {
		//log_message('required',$this->_iMemberId.' contract::ajax_update_team post '.print_r($_POST,true));
		//return false;

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			echo json_encode(array(
				'success' => 0
				,'error'  => 'Contract Not Found'
			));
			return false;
		}

		$oCMS = Service::load('contractmember');
		$oCMS->deleteContractMembers(array(
			'contract_id' => $iContractId
		));

		$aNewUsers = array();
		$aCleanedUsers = array();
		$aRequestedUserIds = array();
		$aActualUserIds = array($this->_iMemberId);

		//log_message('required','ajax_update_team:'._LINE__.' $_POST -> '.print_r($_POST,true));
		$aUserData = (!empty($_POST['users']))?$_POST['users']:array();

		if (!empty($aUserData)) {
			list($aRequestedUserIds,$aCleanedUsers,$aNewUsers) = $this->_cleanPostedUserData($aUserData);
			//log_message('required','ajax_update_team:'._LINE__.' $aCleanedUsers -> '.print_r($aCleanedUsers,true));

			if (!empty($aRequestedUserIds)) {
				$oUsers = Service::load('member')->getMembers(array('member_id' => $aRequestedUserIds, 'parent_id' => $this->_iParentId));

				$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccount(array(
					'parent_id' => $this->_iParentId,
					'member_id' => $aRequestedUserIds
				));

				$aUsers = array_merge($oUsers->getResults(),$oOtherMemberAccounts->getResults());

				if (!empty($aUsers)) {
					$aActualUserIds = array();
					foreach ($aUsers as $oUser) {
						$aActualUserIds[] = $oUser->member_id;
					}

					foreach ($aCleanedUsers as $iMemberId=>$iLevel) {
						if (!in_array($iMemberId,$aActualUserIds)) {
							unset($aCleanedUsers[$iMemberId]);
						}
					}

					if (!empty($aCleanedUsers)) {
						foreach ($aCleanedUsers as $iUserId => $iLevel) {
							$oCMS->addContractMember(new ContractMemberModel(array(
								'contract_id'   => $iContractId
								, 'member_id'   => $iUserId
								, 'level'       => $iLevel
								, 'create_date' => date('Y-m-d H:i:s')
							)));
						}
					}
				} else {
					//log_message('required','contract::ajax_finish_upload no user in db');
				}

			} else {
				//log_message('required','contract::ajax_update_team no userids');
			}
		}

		$oCAS = Service::load('contractapproval');
		$oCAS->deleteContractApprovals(array('contract_id'=>$iContractId));
		$bAddedSteps = false;
		if (!empty($_POST['workflow'])) {
			//log_message('required','contract::ajax_update_team starting workflow '.print_r($_POST['workflow'],true));
			$iStepId = 1;
			$iStepStatus = ContractApprovalModel::STATUS_PENDING;

			foreach ($_POST['workflow'] as $aStep) {
				if (empty($aStep['type']) || empty($aStep['members'])) {
					continue;
				}

				$iStepType = ContractApprovalModel::TYPE_ALL;
				if (strcasecmp('Require Any',$aStep['type']) === 0) {
					$iStepType = ContractApprovalModel::TYPE_ANY;
				}

				foreach ($aStep['members'] as $mStepMember) {
					if (is_numeric($mStepMember)) {
						$iStepMemberId = $mStepMember;
					} elseif ($aNewUsers[$mStepMember]) {
						$iStepMemberId = $aNewUsers[$mStepMember];
					} else {
						continue;
					}

					if (!in_array($iStepMemberId,$aActualUserIds)) {
						continue;
					}

					//log_message('required','contract::ajax_update_team adding step');
					Service::load('contractapproval')->addContractApproval(new ContractApprovalModel(array(
						'contract_id'  => $iContractId
						,'member_id'   => $iStepMemberId
						,'step'        => $iStepId
						,'type'        => $iStepType
						,'status'      => $iStepStatus
						,'create_date' => date('Y-m-d H:i:s')
					)));
					$bAddedSteps = true;

					if ($iStepId == 1) {
						$oMemberToNotify = Service::load('member')->getMember(array('member_id'=>$iStepMemberId))->reset();
						if (!empty($oMemberToNotify)) {
							$this->_sendApprovalNotification($oMemberToNotify,$oContract);
						}
					}
				}

				$iStepId++;
				$iStepStatus = ContractApprovalModel::STATUS_WAITING;
			}
		}

		$oCSS = Service::load('contractsignature');
		$oCSS->deleteContractSignatures(array('contract_id'=>$iContractId));
		if (!empty($_POST['signatures'])) {
			$bSigsAdded = false;
			foreach ($_POST['signatures'] as $mSignatory) {
				if (is_numeric($mSignatory)) {
					$iSignatory = $mSignatory;
				} elseif ($aNewUsers[$mSignatory]) {
					$iSignatory = $aNewUsers[$mSignatory];
				} else {
					//log_message('error','ajax_update_team:'.__LINE__.' not found: '.$mSignatory);
					continue;
				}

				//log_message('error','ajax_update_team:'.__LINE__.' found: '.$mSignatory.' '.$iSignatory);

				if (!in_array($iSignatory,$aActualUserIds)) {
					continue;
				}

				$oCSS->addContractSignature(new ContractSignatureModel(array(
					'contract_id'  => $iContractId
					,'member_id'   => $iSignatory
					,'create_date' => date('Y-m-d H:i:s')
				)));
				$bSigsAdded = true;
			}

			if (empty($bSigsAdded)) {
				$this->session->success('Your document(s) will be sent to DocuSign shortly.');

				Service::load('docusigncontract')->addDocusignContract(new DocusignContractModel(array(
					'contract_id'  => $iContractId
					,'status'      => DocusignContractModel::STATUS_PENDING
					,'create_date' => date('Y-m-d H:i:s')
				)));
			}
		}

		echo json_encode(array('success'=>1));
	}

	protected function _sendContractInDocuSignNotification($oMemberToNotify,$oContract,$bReminder=false) {
		$sSubject = $this->lang->line('contract_approval_notify_subject_'.($bReminder?'reminder_':'').'text');

		$oUploader = Service::load('member')->getMembers(array('member_id'=>$oContract->owner_id))->reset();

		$sMessageHTML = $this->load->view('emails/approval_notify',array(
			'iContractId'    => $oContract->contract_id
			,'sUploaderName' => $oUploader->name?$oUploader->name:$oUploader->email
			,'sDate'         => convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%x')
//date('n/j/Y',strtotime($oContract->last_updated))
			,'sFilename'     => $oContract->file_name
		),true);

		$sMessageText = $this->lang->line('contract_approval_notify_message_text');
		$sMessageText = str_replace('%%CONTRACT_ID%%',$oContract->contract_id,$sMessageText);
		$sMessageText = str_replace('%%UPLOADER_NAME%%',$oUploader->name?$oUploader->name:$oUploader->email,$sMessageText);
		$sMessageText = str_replace('%%UPLOAD_DATE%%',convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%x')
			/*date('n/j/Y',strtotime($oContract->last_updated))*/,$sMessageText);
		$sMessageText = str_replace('%%FILENAME%%',$oContract->file_name,$sMessageText);
		$sMessageText = str_replace('%%URLTOCONTRACT%%',site_url('/contracts/view/'.$oContract->contract_id),$sMessageText);

		$bSent = HelperService::sendEmail(
			$oMemberToNotify->email,
			ConfigService::getItem('support_email'),
			$sSubject,
			$sMessageText,
			$sMessageHTML
		);
		log_message('required','contract_approval_notify email sent: '. ($bSent?1:0));
		return $bSent;
	}

	protected function _sendApprovalNotification($oMemberToNotify,$oContract,$bReminder=false) {
		$sSubject = $this->lang->line('contract_approval_notify_subject_'.($bReminder?'reminder_':'').'text');

		$oUploader = Service::load('member')->getMembers(array('member_id'=>$oContract->owner_id))->reset();

		$sMessageHTML = $this->load->view('emails/approval_notify',array(
			'iContractId'    => $oContract->contract_id
			,'sUploaderName' => $oUploader->name?$oUploader->name:$oUploader->email
			,'sDate'         => convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%x')
//date('n/j/Y',strtotime($oContract->last_updated))
			,'sFilename'     => $oContract->file_name
		),true);

		$sMessageText = $this->lang->line('contract_approval_notify_message_text');
		$sMessageText = str_replace('%%CONTRACT_ID%%',$oContract->contract_id,$sMessageText);
		$sMessageText = str_replace('%%UPLOADER_NAME%%',$oUploader->name?$oUploader->name:$oUploader->email,$sMessageText);
		$sMessageText = str_replace('%%UPLOAD_DATE%%',convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%x')
			/* date('n/j/Y',strtotime($oContract->last_updated))*/,$sMessageText);
		$sMessageText = str_replace('%%FILENAME%%',$oContract->file_name,$sMessageText);
		$sMessageText = str_replace('%%URLTOCONTRACT%%',site_url('/contracts/view/'.$oContract->contract_id),$sMessageText);

		$bSent = HelperService::sendEmail(
			$oMemberToNotify->email,
			ConfigService::getItem('support_email'),
			$sSubject,
			$sMessageText,
			$sMessageHTML
		);
		log_message('required','contract_approval_notify email sent: '. ($bSent?1:0));
		return $bSent;
	}

	public function update_team($iContractId) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('contracts');
		}

		$oDocuSignContracts = Service::load('docusigncontract')->getDocusignContracts(array(
			'contract_id' => $iContractId
			,'status'     => array(
				DocusignContractModel::STATUS_SENT_TO_DOCUSIGN
				,DocusignContractModel::STATUS_SENT_TO_SIGNERS
			)
		));
		if ($oDocuSignContracts->count) {
			$this->session->error('You can not update teams or workflows while document is in DocuSign.');
			redirect('contracts/view/'.$iContractId);
		}

		$bCurrentUserCanEdit = false;
		if ($this->_iMemberId == $oContract->owner_id ||
			$this->_iMemberId == $oContract->parent_id) {
			$bCurrentUserCanEdit = true;
		}

		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $iContractId
		));

		$aTeamMemberIds = array($oContract->owner_id);
		foreach ($oTeamMembers as $oTeamMember) {
			$aTeamMemberIds[] = $oTeamMember->member_id;
		}

		$aTeamMembers = array();
		if (!empty($aTeamMemberIds)) {
			$oMembers = Service::load('member')->getMembers(array(
				'member_id'  => $aTeamMemberIds
				,'parent_id' => $this->_iParentId
			));

			$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccountsWithMemberAccountData(array(
				'other_member_accounts.parent_id' => $this->_iParentId
				,'other_member_accounts.member_id' => $aTeamMemberIds
			));

			$aUsers = array_merge($oMembers->getResults(),$oOtherMemberAccounts->getResults());

			foreach ($aUsers as $oMember) {
				if ($oContract->owner_id == $oMember->member_id) {
					$oMember->level = 'Owner';
				} else {
					foreach ($oTeamMembers as $oTeamMember) {
						if ($oTeamMember->member_id == $oMember->member_id) {
							$oMember->level = $oTeamMember->readable_level;
							if ($oTeamMember->member_id == $this->_iMemberId && $oTeamMember->level == ContractMemberModel::LEVEL_EDITOR) {
								$bCurrentUserCanEdit = true;
							}
						}
					}
				}
				$aTeamMembers[$oMember->member_id] = $oMember;
			}
		}

		if (!$bCurrentUserCanEdit) {
			$this->session->error('You do not have permission to edit this contract.');
			redirect('contracts/view/'.$iContractId);
		}

		$oAccountMembers = Service::load('member')->getMembers(array(
			'parent_id' => $this->_iParentId
			,'status'   => array(MemberModel::StatusActive,MemberModel::StatusPending)
		));

		$oOtherAccountMembers = Service::load('othermemberaccount')->getOtherMemberAccountsWithMemberAccountData(array(
			'other_member_accounts.parent_id' => $this->_iParentId
		));

		$aUnsortedMembers = array_merge($oAccountMembers->results(),$oOtherAccountMembers->results());
		$aMembers = array();
		foreach ($aUnsortedMembers as $oUnsortedMember) {
			$aMembers[$oUnsortedMember->member_id] = $oUnsortedMember;
		}
		unset($aUnsortedMembers);
		if (!empty($_GET['show_debug'])) {
			echo '<pre>'; var_dump($oContract,$aMembers); return false;
		}

		if ($this->_isPost()) {
			//echo '<pre>'; var_dump($_POST); return false;

			$sNewMember = !empty($_POST['new_member'])?trim($_POST['new_member']):'';
			if (!empty($sNewMember)) {
				foreach ($aMembers as $oMember) {
					if (strcmp($oMember->email,$sNewMember)===0) {
						$oAdd = Service::load('contractmember')->addContractMember(new ContractMemberModel(array(
							'contract_id'  => $iContractId
							,'member_id'   => $oMember->member_id
							,'create_date' => date('Y-m-d H:i:s')
						)));

						if ($oAdd->isOk()) {
							$this->session->success('Contract access added.');
							redirect('/contracts/view/'.$iContractId);
						} else {
							$this->session->current_error('Unable to add access.');
						}
					}
				}

				$this->session->current_error('User not found.');
			} else {
				$this->session->current_error('User email required.');
			}
		}

		$aApprovalSteps = Service::load('contractapproval')->getContractApprovalsWithAssignees($oContract->contract_id)->getResults();

		$aApprovalStepsSorted = array();
		foreach ($aApprovalSteps as $oApprovalStep) {
			if (!isset($aApprovalStepsSorted[$oApprovalStep->step]['status'])) {
				$aApprovalStepsSorted[$oApprovalStep->step]['status'] = ContractApprovalModel::STATUS_PENDING;
			}

			$aApprovalStepsSorted[$oApprovalStep->step]['steps'][] = $oApprovalStep;
			$aApprovalStepsSorted[$oApprovalStep->step]['type'] = $oApprovalStep->type;

			if ($oApprovalStep->status != ContractApprovalModel::STATUS_SKIPPED &&
				$oApprovalStep->status > $aApprovalStepsSorted[$oApprovalStep->step]['status']) {
				$aApprovalStepsSorted[$oApprovalStep->step]['status'] = $oApprovalStep->status;
			}
		}
		$this->set('aApprovalStepsSorted',$aApprovalStepsSorted);
		$this->set('bCurrentSubHasApprovalAccess',$this->_getSubscription()->approvals);

		$oSignatures = Service::load('contractsignature')->getContractSignatures(array(
			'contract_id' => $iContractId
		));
		$aSignatureMembers = array();
		$aSignatureMembersToGet = array();
		foreach ($oSignatures as $oSignature) {
			if (!empty($aTeamMembers[$oSignature->member_id])) {
				$aSignatureMembers[$oSignature->member_id] = $aTeamMembers[$oSignature->member_id];
			} else {
				$aSignatureMembersToGet[$oSignature->member_id] = $oSignature->member_id;
			}
		}
		if (!empty($aSignatureMembersToGet)) {
			$oSignatureMembersToSort = Service::load('member')->getMembers(array('member_id'=>$aSignatureMembersToGet));
			foreach ($oSignatureMembersToSort as $oSignatureMemberToSort) {
				$aSignatureMembers[$oSignature->member_id] = $aTeamMembers[$oSignature->member_id];
			}
		}

		$this->set('oSignatures',$oSignatures);
		$this->set('aSignatureMembers',$aSignatureMembers);

		$this->set('oContract',$oContract);
		$this->set('aTeamMembers',$aTeamMembers);
		$this->set('aMembers',$aMembers);

		if (can_access_feature('docusign',$this->_iMemberId)) {
			$this->load->view('contracts/update_team_signatures',$this->aData);
		} else {
			$this->load->view('contracts/update_team_approvals',$this->aData);
		}
	}

	public function ajax_update_users($iContractId) {
		log_message('required','::ajax_update_users '.$iContractId.' '.print_r($_POST,true));

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $iContractId
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			echo json_encode(array('success'=>0,'error'=>'Contract not found.'));
			return false;
		}

		$oCMS = Service::load('contractmember');
		$oTeamMembers = $oCMS->getContractMembers(array(
			'contract_id' => $iContractId
		));

		$aTeamMembers = array();
		foreach ($oTeamMembers as $oTeamMember) {
			$aTeamMembers[$oTeamMember->member_id] = $oTeamMember;
		}

		$aCleanReqs = array();
		if (!empty($_POST['user_updates']) && is_array($_POST['user_updates'])) {
			foreach ($_POST['user_updates'] as $sUserUpdate) {
				$aUpdate = explode('//',$sUserUpdate);
				if (empty($aUpdate[1])) {
					continue;
				}

				$sNewStatus = strtolower(str_replace(' ','',$aUpdate[1]));
				if (!in_array($sNewStatus,array('editor','viewonly','removed'))) {
					continue;
				}

				if (!isset($aTeamMembers[$aUpdate[0]])) {
					continue;
				}

				$aCleanReqs[$aUpdate[0]] = $sNewStatus;
			}
		}

		log_message('required','::ajax_update_users '.$iContractId.' cleaned '.print_r($aCleanReqs,true));
		foreach ($aTeamMembers as $iTeamMemberId=>$oTeamMember) {
			if (isset($aCleanReqs[$oTeamMember->member_id])) {
				$sNewStatus = $aCleanReqs[$oTeamMember->member_id];
				if ($sNewStatus == 'removed') {
					$oCMS->deleteContractMembers(array('contract_member_id'=>$oTeamMember->contract_member_id));
				} elseif ($sNewStatus == 'viewonly' && $oTeamMember->level == ContractMemberModel::LEVEL_EDITOR) {
					$oTeamMember->level = ContractMemberModel::LEVEL_VIEW_ONLY;
					$oCMS->updateContractMember($oTeamMember);
				} elseif ($sNewStatus == 'editor' && $oTeamMember->level == ContractMemberModel::LEVEL_VIEW_ONLY) {
					$oTeamMember->level = ContractMemberModel::LEVEL_EDITOR;
					$oCMS->updateContractMember($oTeamMember);
				}
			}
		}

		echo json_encode(array('success'=>1));
	}

	/*public function test_services() {
		$oCS = Service::load('contract')->addContract(new ContractModel(array(
			'owner_id'      => 1
			,'parent_id'    => 1
			,'name'         => 'first contract'
			,'company'      => 'test company'
			,'start_date'   => date('Y-m-d H:i:s')
			,'end_date'     => date('Y-m-d H:i:s',strtotime('+6 months'))
			,'value'        => 100000
			,'type'         => ContractModel::TYPE_BUY_SIDE
			,'status'       => ContractModel::STATUS_ACTIVE
			,'file_name'    => 'fakecontract.txt'
			,'create_date'  => date('Y-m-d H:i:s')
			,'last_updated' => date('Y-m-d H:i:s')
		)),'/var/www/html/ctcssa/fakecontract.txt');
	}*/
}
