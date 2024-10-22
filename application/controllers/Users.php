<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Users extends User_Controller {

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

	/*public function test_int() {
		$this->load->library('Intercom');
		$mResponse = $this->intercom->createUser('jtravis@shattereddigital.com','Jonathan Travis 2');
		echo '<pre>'; var_dump($mResponse);
	}*/

	/**
	 * Main view contracts
	 *
	 * @access public
	 */
	public function index() {
		if ($this->_iMemberId != $this->_iParentId) {
			redirect('welcome');
		}

		$oMembers = Service::load('member')->getMembers(array(
			'parent_id'  => $this->_iParentId
			,'status !=' => MemberModel::StatusDeleted
		));

		$this->session->set_userdata('member_last_page','/users');

		$aMemberIds = array();
		foreach ($oMembers as $oMember) {
			$aMemberIds[] = $oMember->member_id;
		}
		$aMembers = $oMembers->getResults();

		$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccount(array(
			'parent_id' => $this->_iParentId
		));

		if ($oOtherMemberAccounts->count) {
			$aOtherMemberIds = array();
			foreach ($oOtherMemberAccounts as $oOtherMemberAccount) {
				$aOtherMemberIds[] = $oOtherMemberAccount->member_id;
			}
			$oOtherMembers = Service::load('member')->getMembers(array('member_id'=>$aOtherMemberIds));

			$aMembers = array_merge($oMembers->getResults(),$oOtherMembers->getResults());
		}

		$this->set('aContractCountsPerMember',Service::load('contract')->getContractCountsForMembers($aMemberIds)->getResults());
		$this->set('sHeader','User Management');
		$this->set('aMembers',$aMembers);
		$this->build('users/index');
	}

	public function add_user() {
		if ($this->_iMemberId != $this->_iParentId) {
			redirect('welcome');
		}

		if (empty($_POST['add_email']) || !filter_var($_POST['add_email'],FILTER_VALIDATE_EMAIL)) {
			$this->session->error('Invalid email provided.');
			redirect('users');
		}

		$sEmail = strtolower(trim($_POST['add_email']));

		$oMember = Service::load('member')->getMembers(array('email'=>$sEmail))->reset();
		if (!empty($oMember)) {
			if ($oMember->status == MemberModel::StatusDeleted && $oMember->parent_id == $this->_iMemberId) {
				$oMember->status = MemberModel::StatusPending;
				Service::load('member')->updateMember($oMember);
				$bSent = Service::load('member')->sendSubaccountConfirmationEmail($oMember);
				if ($bSent) {
				    send_analytic_event('User Invitation Email Sent', null, ['invited_email' => $sEmail]);
				}
				
				$this->session->success('User invite sent.');
				redirect('users');

			} elseif ($oMember->status == MemberModel::StatusDeleted && $oMember->parent_id == $this->_iParentId) {
				$this->session->error('User is already associated to this account but not available.');
				redirect('users');

			} elseif ($oMember->parent_id == $this->_iParentId) {
				$this->session->error('User is already associated to this account.');
				redirect('users');

			} else {
				$oOtherMemberAccount = Service::load('othermemberaccount')->getOtherMemberAccount(array(
					'member_id' => $oMember->member_id
					,'parent_id' => $this->_iParentId
				))->first();

				if (!empty($oOtherMemberAccount)) {
					$this->session->error('User is already associated to this account.');
					redirect('users');
				}

				Service::load('othermemberaccount')->addOtherMemberAccount(new OtherMemberAccountModel(array(
					'member_id' => $oMember->member_id
					,'parent_id' => $this->_iParentId
					,'create_date' => date('Y-m-d H:i:s')
				)));

				$this->session->success('User is now associated to this account.');
				redirect('users');
			}
		}
		
		$parent = Service::load('member')->getMembers(array(
		    'member_id'  => $this->_iParentId
		))->reset();

		$oMember = Service::load('member')->addMember(new MemberModel(array(
			'email'        => $sEmail
			,'parent_id'   => $this->_iParentId
		    ,'country_id'  => $parent->country_id
		    ,'currency'    => $parent->currency
			,'password'    => md5($sEmail.md5($sEmail.'temppasswordSalt3dHA3h'.time().$this->_iParentId).'estraSalthe@#$%@#!$%^@$#%@'.time())
			,'create_date' => date('Y-m-d H:i:s')
		)))->reset();

		$bSent = Service::load('member')->sendSubaccountConfirmationEmail($oMember);
		if ($bSent) {
		    send_analytic_event('User Invitation Email Sent', null, ['invited_email' => $sEmail]);
		}
		
		$this->session->success('User invite sent.');
		redirect('users');
	}

	public function profile($iMemberId) {
		$oMember = Service::load('member')->getMembers(array(
			'member_id'  => $iMemberId
		))->reset();

		if (empty($oMember)) {
			$this->session->error('User not found.');
			redirect('users');
		}

		if ($oMember->parent_id != $this->_iParentId) {
			$oOtherMemberAccount = Service::load('othermemberaccount')->getOtherMemberAccount(array(
				'member_id' => $oMember->member_id
				,'parent_id' => $this->_iParentId
			))->first();

			if (empty($oOtherMemberAccount)) {
				$this->session->error('User not found.');
				redirect('users');
			}
		}

		$oContracts = Service::load('contract')->getContractsByTeamMember($iMemberId,$this->_iParentId,'cs.create_date desc',200);
		$aContractIdsWithAccess = array();
		if ($oContracts->count) {
			$aOwnerIds = array();
			$aContractIds = array();
			foreach ($oContracts as $oContract) {
				$aOwnerIds[$oContract->owner_id] = $oContract->owner_id;
				$aContractIds[] = $oContract->contract_id;
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

		$this->set('oContracts',$oContracts);
		$this->set('aContractIdsWithAccess',$aContractIdsWithAccess);
		$this->set('oMember',$oMember);
		$this->load->view('users/profile',$this->aData);
	}

	public function profile_admin($iMemberId) {
		if ($this->_iParentId != $this->_iMemberId) {
			redirect('welcome');
		}

		$oMember = Service::load('member')->getMembers(array(
			'member_id'  => $iMemberId
		))->reset();

		if (empty($oMember)) {
			$this->session->error('User not found.');
			redirect('users');
		}

		if ($oMember->parent_id != $this->_iParentId) {
			$oOtherMemberAccount = Service::load('othermemberaccount')->getOtherMemberAccount(array(
				'member_id' => $oMember->member_id
				,'parent_id' => $this->_iParentId
			))->first();

			if (empty($oOtherMemberAccount)) {
				$this->session->error('User not found.');
				redirect('users');
			}
		}

		Service::load('contractmember');
		$aContractIdsWithAccess = array();
		$oContracts = Service::load('contract')->getContractsByTeamMember($iMemberId,$this->_iParentId,'cs.create_date desc',200);
		if ($oContracts->count) {
			$aOwnerIds = array();
			$aContractIds = array();
			foreach ($oContracts as $oContract) {
				$aOwnerIds[$oContract->owner_id] = $oContract->owner_id;
				$aContractIds[] = $oContract->contract_id;
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

		$iCountOwner = 0;
		$iCountEditor = 0;
		$iCountReadOnly = 0;

		foreach ($oContracts as $oContract) {
			if ($oContract->owner_id == $oMember->member_id) {
				$iCountOwner++;
			} elseif (is_null($oContract->level)) {
			} elseif ($oContract->level == ContractMemberModel::LEVEL_EDITOR) {
				$iCountEditor++;
			} elseif ($oContract->level == ContractMemberModel::LEVEL_VIEW_ONLY) {
				$iCountReadOnly++;
			}
		}

		$sLastPage = $this->session->userdata('member_last_page');
		if (!empty($sLastPage)) {
			$this->set('sLastPage',$sLastPage);
		}

		$this->set('oAccessLogs',Service::load('memberaccesslog')->getMemberAccessLogs(array('member_id'=>$oMember->member_id)));
		$this->set('aContractIdsWithAccess',$aContractIdsWithAccess);
		$this->set('oContracts',$oContracts);
		$this->set('iCountOwner',$iCountOwner);
		$this->set('iCountEditor',$iCountEditor);
		$this->set('iCountReadOnly',$iCountReadOnly);
		$this->set('oMember',$oMember);
		$this->load->view('users/profile_admin',$this->aData);
	}

	public function suspend($iMemberId) {
		if ($this->_iMemberId != $this->_iParentId) {
			redirect('welcome');
		}

		if ($iMemberId == $this->_iMemberId) {
			$this->session->error('You can not suspend yourself.');
			redirect('users');
		}

		$oUser = Service::load('member')->getMembers(array('member_id'=>$iMemberId,'parent_id'=>$this->_iMemberId))->reset();

		if (empty($oUser)) {
			$this->session->error('User not found.');
			redirect('users');
		}

		$oUser->status = MemberModel::StatusSuspended;
		Service::load('member')->updateMember($oUser);
		$this->session->success('User suspended.');
		redirect('users');
	}

	public function delete_user($iMemberId) {
		if ($this->_iMemberId != $this->_iParentId) {
			redirect('welcome');
		}

		if ($iMemberId == $this->_iMemberId) {
			$this->session->error('You can not suspend yourself.');
			redirect('users');
		}

		$oUser = Service::load('member')->getMembers(array('member_id'=>$iMemberId,'parent_id'=>$this->_iMemberId))->reset();

		if (empty($oUser)) {
			$this->session->error('User not found.');
			redirect('users');
		}

		$sSubject = ConfigService::getItem('app_name').' - Account Notice';

		$sMessageHTML = $this->load->view('emails/account_notice', array() ,true);

		$sMessageText = "Your account has been removed from Contract Hound. You will no longer receive email updates about this account.";

		$bSent = HelperService::sendEmail(
				$oUser->email,
				ConfigService::getItem('support_email'),
				$sSubject,
				$sMessageText,
				$sMessageHTML
		);
		log_message('required','account_notice email sent: '. $bSent?1:0);

		$oUser->status = MemberModel::StatusDeleted;
		Service::load('member')->updateMember($oUser);
		
		send_analytic_event('User Deleted', null, ['deletedUserId' => $oUser->member_id, 'deletedUserEmail' => $oUser->email]);
		
		$this->session->success('User deleted.');
		redirect('users');
	}
}