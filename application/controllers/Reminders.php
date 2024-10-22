<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Reminders extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Contract Log Validation
	 *
	 * @access protected
	 */
	protected $reminder_validation = array(
		array(
			'field' => 'message',
			'label' => 'message',
			'rules' => 'trim|required|max_length[2000]'
		),
		array(
			'field' => 'alert_date',
			'label' => 'Alert Date',
			'rules' => 'trim|required'
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

	public function test_reminder() {
		$oReminder = Service::load('reminder')->getReminders(array(
			'reminder_id'    => 5
		))->reset();
		$oMember = Service::load('member')->getMembers(array(
			'member_id' => 1
		))->reset();
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => 22
		))->reset();
		$sUrl = site_url().'contracts/view/'.$oContract->contract_id;
		$sMessageHTML = $this->load->view('emails/reminder_single',array(
			'oReminder'  => $oReminder
			,'oContract' => $oContract
			,'sEmail'    => $oMember->email
			,'sUrl'      => $sUrl
		),true);
		echo $sMessageHTML;
	}

	/**
	 * Validate and Convert Alert Date
	 *
	 * @access public
	 * @param string $sAlertDate
	 * @return bool
	 */
	public function _validate_convert_alert_date($sAlertDate) {
		if (!preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/',$sAlertDate)) {
			$this->form_validation->set_message('_validate_convert_alert_date', 'Invalid Alert Date');
			return false;
		}

		$aDate = explode('/',$sAlertDate);
		$aDate[0] = str_pad($aDate[0],2,'0',STR_PAD_LEFT);
		$aDate[1] = str_pad($aDate[1],2,'0',STR_PAD_LEFT);

		$sAlertDate = "{$aDate[2]}-{$aDate[0]}-{$aDate[1]} 00:00:00";
		if (time() > strtotime($sAlertDate)) {
			$this->form_validation->set_message('_validate_convert_alert_date', 'Invalid Alert Date');
			return false;
		}

		return $sAlertDate;
	}

	/**
	 * Main view contracts
	 *
	 * @access public
	 */
	public function index() {
		redirect('welcome');
	}

	/**
	 * Add reminder
	 *
	 * @access public
	 */
	public function add($iContractId=null) {
		if (empty($iContractId) && $this->_isPost()) {
			$iContractId = $this->input->my_post('contract_id');
		}

		if (!empty($iContractId)) {
			$oContract = Service::load('contract')->getContracts(array(
				'contract_id' => $iContractId
				,'parent_id'  => $this->_iParentId
				,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
			))->first();

			if (empty($oContract)) {
				$this->session->error('Contract not found.');
				redirect('contracts');
			}

			$this->set('iContractId',$iContractId);
			$this->set('oContract',$oContract);
		}

		// get contract team members
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
						}
					}
				}
				$aTeamMembers[$oMember->member_id] = $oMember;
			}
		}

		if ($this->_isPost()) {
			//echo '<pre>'; var_dump($_POST); return false;
			$this->form_validation->set_rules($this->reminder_validation);
			if ($this->form_validation->run()) {
				Service::load('reminder');
				$oReminder = new ReminderModel(array(
					'contract_id' => $iContractId
				));

				foreach ($this->reminder_validation as $aRule) {
					if($aRule['field'] == 'alert_date'){
						$oReminder->alert_date = convert_utc_datetime($this->input->post($aRule['field']),$this->cTimeZone);
					}else{
						$oReminder->setField($aRule['field'], set_value($aRule['field']));
					}
				}
				$oReminder->create_date = date('Y-m-d H:i:s');
				$oAdd = Service::load('reminder')->addReminder($oReminder);
				/*if ($this->_iMemberId == 1) {
					echo '<pre>'; var_dump($_POST,$oReminder,$oAdd); return false;
				}*/

				if ($oAdd->isOk()) {
					$oReminder = $oAdd->first();

					if (!empty($_POST['reminder_members'])) {
						$oRMS = Service::load('remindermember');
						foreach ($_POST['reminder_members'] as $iMemberId) {
							if (empty($aTeamMembers[$iMemberId])) {
								continue;
							}

							$oRMS->addReminderMember(new ReminderMemberModel(array(
								'reminder_id'  => $oReminder->reminder_id
								,'member_id'   => $iMemberId
								,'create_date' => date('Y-m-d H:i:s')
							)));
						}
					}
					
					send_analytic_event('Reminder Created', null, ['reminderId' => $oReminder->reminder_id, 'reminderMessage' => $oReminder->message]);

					$this->session->success('Reminder added.');
				} else {
					$this->session->current_error('Unable to add reminder.');
				}
				redirect('contracts/view/'.$iContractId);
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		$this->set('aTeamMembers',$aTeamMembers);
		$this->load->view('reminders/add',$this->aData);
	}

	/**
	 * Edit reminder
	 *
	 * @access public
	 */
	public function edit($iReminderId) {
		$oReminder = Service::load('reminder')->getReminders(array('reminder_id'=>$iReminderId))->first();
		if (empty($oReminder)) {
			$this->session->error('Reminder not found.');
			redirect('welcome');
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $oReminder->contract_id
			,'parent_id'  => $this->_iParentId
			,'status'     => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		))->first();

		if (empty($oContract)) {
			$this->session->error('Contract not found.');
			redirect('welcome');
		}

		// get contract team members
		$oTeamMembers = Service::load('contractmember')->getContractMembers(array(
			'contract_id' => $oContract->contract_id
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
						}
					}
				}
				$aTeamMembers[$oMember->member_id] = $oMember;
			}
		}

		$oReminderMembers = Service::load('remindermember')->getReminderMembers(array('reminder_id'=>$iReminderId));
		$aReminderMembers = array();
		foreach ($oReminderMembers as $oReminderMember) {
			$aReminderMembers[] = $oReminderMember->member_id;
		}

		if ($this->_isPost()) {
			//echo '<pre>'; var_dump($_POST); return false;
			//log_message('required','Reminders::edit post: '.print_r($_POST,true));
			$this->form_validation->set_rules($this->reminder_validation);
			if ($this->form_validation->run()) {
				foreach ($this->reminder_validation as $aRule) {
					if($aRule['field'] == 'alert_date'){
						$oReminder->alert_date = convert_utc_datetime($this->input->post($aRule['field']),$this->cTimeZone);
					}else{
						$oReminder->setField($aRule['field'], set_value($aRule['field']));
					}
				}

				$oUpdate = Service::load('reminder')->updateReminder($oReminder);

				if ($oUpdate->isOk()) {
					if (!empty($_POST['reminder_members'])) {
						$oRMS = Service::load('remindermember');
						foreach ($aReminderMembers as $iMemberId) {
							if (!in_array($iMemberId,$_POST['reminder_members'])) {
								$oRMS->deleteReminderMembers(array('reminder_id'=>$iReminderId,'member_id'=>$iMemberId));
								//log_message('required','Reminders::edit removing member: '.$iMemberId);
							}
						}

						foreach ($_POST['reminder_members'] as $iMemberId) {
							if (empty($aTeamMembers[$iMemberId])) {
								//log_message('required','Reminders::edit not in teammember: '.$iMemberId);
								continue;
							}

							if (in_array($iMemberId,$aReminderMembers)) {
								//log_message('required','Reminders::edit already on reminder: '.$iMemberId);
								continue;
							}

							//log_message('required','Reminders::edit adding to reminder: '.$iMemberId);
							$oRMS->addReminderMember(new ReminderMemberModel(array(
								'reminder_id'  => $oReminder->reminder_id
								,'member_id'   => $iMemberId
								,'create_date' => date('Y-m-d H:i:s')
							)));
						}
					}

					$this->session->success('Reminder updated.');
				} else {
					$this->session->current_error('Unable to update reminder.');
				}
				redirect('contracts/view/'.$oContract->contract_id);
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		//echo '<pre>'; var_dump($aReminderMembers,$aTeamMembers); return true;
		$this->set('oContract',$oContract);
		$this->set('oReminder',$oReminder);
		$this->set('aTeamMembers',$aTeamMembers);
		$this->set('aReminderMembers',$aReminderMembers);
		$this->load->view('reminders/edit',$this->aData);
	}

	public function dismiss($iReminderId) {
		$oReminder = Service::load('reminder')->getReminders(array('reminder_id'=>$iReminderId))->first();
		if (empty($oReminder)) {
			$this->session->error('Reminder not found.');
			redirect('welcome');
		}

		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $oReminder->contract_id
			,'parent_id'  => $this->_iParentId
		))->first();
		if (empty($oContract)) {
			$this->session->error('Reminder not found.');
			redirect('welcome');
		}

		if ($oContract->owner_id != $this->_iMemberId) {
			$oReminderMember = Service::load('remindermember')->getReminderMembers(array(
				'reminder_id'=>$iReminderId
				,'member_id' => $this->_iMemberId
			))->first();
			if (empty($oReminderMemberoReminderMember)) {
				$this->session->error('Reminder not found.');
				redirect('welcome');
			}
		}

		$oReminder->status = ReminderModel::STATUS_COMPLETED;
		Service::load('reminder')->updateReminder($oReminder);
		
		send_analytic_event('Reminder Dismissed', null, ['reminderId' => $oReminder->reminder_id, 'reminderMessage' => $oReminder->message]);
		
		$this->session->success('Reminder dismissed.');
		redirect('welcome');
	}

	public function delete($iReminderId) {
		$oReminder = Service::load('reminder')->getReminders(array('reminder_id'=>$iReminderId))->first();
		if (empty($oReminder)) {
			$this->session->error('Reminder not found.');
			redirect('welcome');
		}

		$oDeleted = Service::load('reminder')->deleteReminders(array('reminder_id'=>$iReminderId));
		if ($oDeleted->isOk()) {
			$this->session->success('Reminder deleted.');
		} else {
			$this->session->error('Reminder could not be deleted.');
		}

		redirect('contracts/view/'.$oReminder->contract_id);
	}
}