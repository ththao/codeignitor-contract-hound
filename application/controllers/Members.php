<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Members extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Signup Validation
	 *
	 * @access protected
	 */
	protected $settings_validation = array(
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|lower|callback__check_email'
		),
		array(
			'field' => 'first_name',
			'label' => 'First Name',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'last_name',
			'label' => 'last Name',
			'rules' => 'trim|max_length[255]'
		),
		'company' => array(
			'field' => 'company',
			'label' => 'Company',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'role',
			'label' => 'Role',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'new_password',
			'label' => 'Password',
			'rules' => 'trim|min_length[6]|max_length[255]'
		),
		array(
			'field' => 'country_code',
			'label' => 'Country',
			'rules' => 'trim|required'
		),
	    array(
	        'field' => 'currency',
	        'label' => 'Currency',
	        'rules' => 'trim|max_length[10]'
	    ),
	);

	/**
	 * Signup Validation
	 *
	 * @access protected
	 */
	protected $signup_validation = array(
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|lower|callback__check_email'
		),
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'trim|required|min_length[6]|max_length[20]'
		),
		array(
			'field' => 'first_name',
			'label' => 'First Name',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'last_name',
			'label' => 'last Name',
			'rules' => 'trim|max_length[255]'
		),
		'company' => array(
			'field' => 'company',
			'label' => 'Company',
			'rules' => 'trim|max_length[255]'
		),
	    'country_id' => array(
	        'field' => 'country_code',
	        'label' => 'Country',
	        'rules' => 'trim|required'
	    ),
		/*array(
			'field' => 'confirm_password',
			'label' => 'Confirm Password',
			'rules' => 'trim|matches[password]'
		),
		array(
			'field' => 'confirm_email',
			'label' => 'Confirm Email',
			'rules' => 'trim|callback__check_confirm_email'
		),*/
	);

	/**
	 * Password Reset Validation
	 *
	 * @access protected
	 */
	protected $password_reset_validation = array(
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'trim|required|min_length[6]|max_length[20]'
		),
		array(
			'field' => 'confirm_password',
			'label' => 'Confirm Password',
			'rules' => 'trim|callback__check_confirm_password'
		),
	);

	/**
	 * Accept Subaccount Validation
	 *
	 * @access protected
	 */
	protected $accept_subaccount_validation = array(
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'trim|required|min_length[6]|max_length[20]'
			),
			array(
				'field' => 'confirm_password',
				'label' => 'Confirm Password',
				'rules' => 'trim|required|matches[password]'
			),
	);

	/**
	 * Update Validation
	 *
	 * @access protected
	 */
	protected $update_validation = array(
		array(
			'field' => 'email',
			'label' => 'lang:member_email_label',
			'rules' => 'trim|required|valid_email|lower|callback__check_email'
		),
		array(
			'field' => 'new_password',
			'label' => 'New Password',
			'rules' => 'trim|min_length[6]|max_length[20]'
		),
		array(
			'field' => 'confirm_new_password',
			'label' => 'Confirm Password',
			'rules' => 'trim|matches[new_password]'
		),
	);

	/**
	 * Profile Validation
	 *
	 * @access protected
	 */
	protected $profile_validation = array(
		array(
			'field' => 'first_name',
			'label' => 'First Name',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'last_name',
			'label' => 'last Name',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'company',
			'label' => 'Company',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'role',
			'label' => 'Role',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'email',
			'label' => 'lang:member_email_label',
			'rules' => 'trim|required|valid_email|lower|callback__check_email'
		),
	);

	public function __construct() {
		parent::__construct();

		$this->load->library('form_validation');
	}

	public function index() {
		redirect(ConfigService::getItem('default_redirect_to'));
	}

	public function init() {
		redirect('members/login');
		$oMember = new MemberModel(array('email'=>'','password'=>''));
		$oResult = Service::load('member')->addMember($oMember);
		echo $oResult;
	}

	public function settings() {
		$oMember = $this->_getMember($this->_iMemberId);
		if ($this->_isPost()) {
			/*if (!empty($_FILES)) {
				echo '<pre>';
				if (empty($_FILES['avatar']['type']) || strpos($_FILES['avatar']['type'],'image/')!==0) {
					echo "not image\n";
				}
				var_dump($_FILES); return true;
			}*/

			$this->form_validation->set_rules($this->settings_validation);
			if ($this->form_validation->run()) {
				if (!empty($_FILES['avatar']['type']) && strpos($_FILES['avatar']['type'],'image/')!==0) {
					$this->session->current_error('Avatar image must be an image file.');
				} else {
					if (!empty($_FILES['avatar']['type'])) {
						$sFileHash = md5($oMember->member_id.'avatar55');
						$sNewAvatarFileName = $sFileHash.str_replace('image/','.',$_FILES['avatar']['type']);
						$oMember->avatar = $sNewAvatarFileName;
					} elseif (!empty($_POST['remove_avatar'])) {
						$oMember->avatar = '';
					}

					$oMember->first_name = set_value('first_name');
					$oMember->last_name = set_value('last_name');
					$oMember->email = set_value('email');
					$oMember->role = set_value('role');
					$oMember->new_password = set_value('new_password');
					$oMember->currency = set_value('currency');
					
					if (isset($_POST['country_code']) && $country_code = $_POST['country_code']) {
					    $country = Service::load('country')->getCountry(['status' => 1, 'country_code' => strtoupper($country_code)])->first();
					    if ($country) {
					        $oMember->country_id = $country->id;
					    }
					}

					if ($this->_iMemberId == $this->_iParentId) {
						$oMember->company = set_value('company');
					}

					$oMember->count_failed_login_attempts = 0;
					$oUpdate = Service::load('member')->updateMember($oMember);
					if ($oUpdate->isOk()) {
						log_message('required','saving profile: '.print_r($oMember,true));
						if (!empty($_FILES['avatar']['type'])) {
							@rename($_FILES['avatar']['tmp_name'],UASDIR.$sNewAvatarFileName);
							@chmod(UASDIR.$sNewAvatarFileName,0644);
						}

						$oMember = $oUpdate->first();
						$this->session->set_userdata('member_email',$oMember->email);
						$this->session->set_userdata('member_name',trim($oMember->first_name.' '.$oMember->last_name));
						$this->session->set_userdata('member_avatar',$oMember->avatar);
						$this->session->current_success('Settings updated.');
					} else {
						log_message('required','failed to update member');
						$this->session->current_error($this->form_validation->first_error());
					}
				}
			}
		}

		$this->set('oAccessLogs',Service::load('memberaccesslog')->getMemberAccessLogs(array('member_id'=>$this->_iMemberId)));
		$this->set('sHeader','Settings');
		$this->set('oMember',$oMember);
		
		$oCountries = Service::load('country')->getCountries(['status' => 1], [['sortorder', 'ASC']])->getResults();
		$currencies = [];
		$country_code = 'us';
		$countries = [];
		if ($oCountries) {
		    foreach ($oCountries as $oCountry) {
		        if ($oCountry->currency) {
		            $currencies[$oCountry->currency] = $oCountry->currency;
		        }
		        if ($oMember->country_id && $oMember->country_id == $oCountry->id) {
		            $country_code = strtolower($oCountry->country_code);
		        }
		        if ($oCountry->country_code) {
                    $countries[strtolower($oCountry->country_code)] = $oCountry->currency;
		        }
		    }
		}
		ksort($currencies);
		$this->set('currencies', $currencies);
		$this->set('countries', $countries);
		$this->set('defaultCountry', $country_code);
		
		$this->build('members/settings');
	}

	public function settings_ajax() {
		$oMember = $this->_getMember($this->_iMemberId);

		if ($this->_isPost()) {
			if (!empty($_FILES['avatar']['type']) && strpos($_FILES['avatar']['type'],'image/')!==0) {
				log_message('required','failed to update member');
				echo json_encode(array('success'=>0, 'error'=>'Avatar image must be an image file.'));
				return;
			}

			//Check if adding or removing avatar
			if (!empty($_FILES['avatar']['type'])) {
				$sOldAvatar = $oMember->avatar;
				$sFileHash = md5($oMember->member_id.'avatar55'.microtime());
				$sNewAvatarFileName = $sFileHash.str_replace('image/','.',$_FILES['avatar']['type']);
				$oMember->avatar = $sNewAvatarFileName;
			} elseif (!empty($_POST['remove_avatar'])) {
				$oMember->avatar = '';
			}

			//Commit the update to the member model
			$oMember->count_failed_login_attempts = 0;
			$oUpdate = Service::load('member')->updateMember($oMember);
			if ($oUpdate->isOk()) {
				if (!empty($sOldAvatar)) {
					@unlink(UASDIR.$sOldAvatar);
				}
				log_message('required','saving profile: '.print_r($oMember,true));
				if (!empty($_FILES['avatar']['type'])) {
					@rename($_FILES['avatar']['tmp_name'],UASDIR.$sNewAvatarFileName);
					@chmod(UASDIR.$sNewAvatarFileName,0644);
				}

				$oMember = $oUpdate->first();
				$this->session->set_userdata('member_avatar',$oMember->avatar);
			} else {
				log_message('required','failed to update member');
				echo json_encode(array('success'=>0, 'error'=>'Failed to update member.'));
				return;
			}
		}

		echo json_encode(array('success'=>1, 'src'=>$oMember->avatar));
	}

	public function contact_preferences() {
		$oMember = $this->_getMember($this->_iMemberId);

		if ($this->_isPost()) {
			//echo '<pre>'; var_dump($_POST); return false;
			if (isset($_POST['email-notifications']) && in_array($_POST['email-notifications'], array(0, 1, 2))) {
				$oMember->notification_frequency = $_POST['email-notifications'];
			}

			$oMember->notify_contract_changes = empty($_POST['notify_contract_changes']) ? 0 : 1;
			$oMember->notify_add_comment = empty($_POST['notify_add_comment']) ? 0 : 1;
			$oMember->notify_board_change = empty($_POST['notify_board_change']) ? 0 : 1;
			$oMember->notify_contract_status_change = empty($_POST['notify_contract_status_change']) ? 0 : 1;
			$oMember->notify_contract_ending = empty($_POST['notify_contract_ending']) ? 0 : 1;

			//echo '<pre>'; var_dump($_POST,$oMember); return false;
			$oUpdate = Service::load('member')->updateMember($oMember);
			if ($oUpdate->isOk()) {
				$oMember = $oUpdate->first();
				$this->session->current_success('Settings updated.');
			} else {
				$this->session->current_error('Unable to update preferences.');
			}
		}

		$this->set('oMember',$oMember);
		$this->build('members/contact_preferences');
	}

	///////////////////////////////////////////////////////////////////////////
	///  Admin   /////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function admin_login_as($iMemberId) {
		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('members/login');
		}
		$oMember = Service::load('member')->getMembers(array('member_id'=>$iMemberId))->first();
		if (empty($oMember)) {
			$this->session->error('Member not found.');
			redirect('members/admin_list');
		}

		$iMemberId = $this->session->userdata('member_id');

		$this->session->set_userdata('admin_member_id',$iMemberId);

		$this->session->set_userdata('member_id',$oMember->member_id);
		$this->session->set_userdata('member_email',$oMember->email);
		$this->session->set_userdata('member_name',trim($oMember->first_name.' '.$oMember->last_name));
		$this->session->set_userdata('member_avatar',$oMember->avatar);
		$this->session->set_userdata('member_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
		$this->session->set_userdata('member_orig_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);

		$this->session->unset_userdata('member_subscription_last_load');
		$this->session->unset_userdata('member_current_subscription');
		$this->session->unset_userdata('uploaded_contract_ids');
		$this->session->unset_userdata('members_last_sidebar_notifications_pull');

		redirect(ConfigService::getItem('default_redirect_to'));
	}

	public function member_login_as($iParentId=null) {
		$oMember = Service::load('member')->getMember(array(
			'member_id' => $this->_iMemberId
		))->reset();

		// load accounts
		$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccountsWithParentAccountData(array(
			'other_member_accounts.member_id' => $this->_iMemberId
		));
		$aAllowedAccountIds = array($oMember->parent_id);
		foreach ($oOtherMemberAccounts as $oOtherMemberAccount) {
			$aAllowedAccountIds[] = $oOtherMemberAccount->parent_id;
		}

		if (!empty($iParentId) && in_array($iParentId,$aAllowedAccountIds)) {
			$oParent = Service::load('member')->getMember(array(
				'member_id' => $iParentId
			))->reset();
			$this->session->unset_userdata('member_subscription_last_load');
			$this->session->unset_userdata('member_current_subscription');
			$this->session->unset_userdata('uploaded_contract_ids');
			$this->session->unset_userdata('members_last_sidebar_notifications_pull');

			$this->session->set_userdata('member_parent_id',$iParentId);

			if ($oMember->parent_id != $iParentId) {
				$sParentName = $oParent->email;
				if ($oParent->first_name && $oParent->last_name) {
					$sParentName = $oParent->first_name . ' ' . $oParent->last_name;
				}
				if ($oParent->company) {
					$sParentName = $oParent->company;
				}
				$this->session->set_userdata('member_switch_parent_name',$sParentName);
			} else {
				$this->session->unset_userdata('member_switch_parent_name');
			}

			redirect('welcome');
		}

		$this->set('oMember',$oMember);
		$this->set('sHeader','Switch Accounts');
		$this->set('oOtherMemberAccounts',$oOtherMemberAccounts);
		$this->load->view('members/member_login_as',$this->aData);
	}

	public function admin_logout() {
		$iAdminMemberId = $this->session->userdata('admin_member_id');
		if (empty($iAdminMemberId)) {
			redirect(ConfigService::getItem('default_redirect_to'));
		}

		$oMember = Service::load('member')->getMembers(array('member_id'=>$iAdminMemberId))->first();
		if (empty($oMember)) {
			$this->session->error('Member not found.');
			redirect(ConfigService::getItem('default_redirect_to'));
		}

		$this->session->unset_userdata('admin_member_id');
		$this->session->unset_userdata('member_subscription_last_load');
		$this->session->unset_userdata('member_current_subscription');

		$this->session->set_userdata('member_id',$oMember->member_id);
		$this->session->set_userdata('member_email',$oMember->email);
		$this->session->set_userdata('member_name',trim($oMember->first_name.' '.$oMember->last_name));
		$this->session->set_userdata('member_avatar',$oMember->avatar);
		$this->session->set_userdata('member_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
		$this->session->set_userdata('member_orig_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
		$this->session->unset_userdata('uploaded_contract_ids');

		redirect('members/admin_list');
	}

	public function admin_list() {
		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('members/login');
		}

		$sSort = 'member_id desc';
		$sSortRequested = $this->input->my_get('s');
		switch ($sSortRequested) {
			case 'e':
			case 'ea':
				$sSort = 'email asc';
				break;
			case 'ed':
				$sSort = 'email desc';
				break;
			case 'm':
			case 'ma':
				$sSort = 'member_id asc';
				break;
			case 'md':
				$sSort = 'member_id desc';
				break;
			case 's':
			case 'sa':
				$sSort = 'status asc';
				break;
			case 'sd':
				$sSort = 'status desc';
				break;
		}
        
		$oMembers = Service::load('member')->getMembers(array(),$sSort);
		$members = [];
		if ($oMembers) {
		    foreach ($oMembers as $oMember) {
		        if ($oMember->member_id == $oMember->parent_id) {
                    $oMember->subscription = $this->_getSubscriptionByMemberId($oMember->member_id);
		        }
		        $members[] = $oMember;
		    }
		}
		$this->set('oMembers',$members);
		$this->set('aAdminIds',$this->config->item('admin_ids'));
		$this->aAdditionalJSFiles[] = 'admin.js';
		$this->build('members/admin_list');
	}

	public function admin_activate($iMemberId) {
		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('members/login');
		}

		$oMember = Service::load('member')->getMembers(array('member_id'=>$iMemberId))->first();

		if (empty($oMember)) {
			$this->session->error('Member Not found.');
			redirect('members/admin_list');
		}

		$oMember->status = MemberModel::StatusActive;
		Service::load('member')->updateMember($oMember);
		$this->session->error('Member Updated.');
		redirect('members/admin_list');
	}

	public function admin_resend_confirmation($iMemberId) {
		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('members/login');
		}

		$oMember = Service::load('member')->getMembers(array('member_id'=>$iMemberId))->first();

		if (empty($oMember)) {
			$this->session->error('Member Not found.');
			redirect('members/admin_list');
		}

		if ($this->_sendConfirmation($oMember)) {
			$this->session->success('Confirmation sent.');
		} else {
			$this->session->error('Unable to send confirmation email.');
		}

		redirect('members/admin_list');
	}

	public function admin_add() {
		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('members/login');
		}

		if ($this->_isPost()) {
			$this->form_validation->set_rules($this->signup_validation);
			if ($this->form_validation->run()) {
				$oMember = new MemberModel(array(
					'create_date' => date('Y-m-d H:i:s')
				));

				foreach ($this->signup_validation as $aRule) {
					$oMember->setField($aRule['field'], set_value($aRule['field']));
				}

				$oMember->status = MemberModel::StatusActive;

				$oAddResponse = Service::load('member')->addMember($oMember);
				if ($oAddResponse->isOk()) {
					$oMember->parent_id = $oMember->member_id;
					Service::load('member')->updateMember($oMember);
					$this->session->success('Member Added.');
					redirect('members/admin_list');
				}
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		$this->set('sPageTitle','Admin Member Edit');
		$this->set('aBreadCrumbs',array(
			array('title' => 'ADMIN')
			,array('title' => 'Members','path'=>'members/admin_list')
			,array('title' => 'Add Member')
		));
		$this->build('members/admin_add');
	}

	public function admin_edit($iMemberId) {
		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('members/login');
		}

		$oMember = Service::load('member')->getMembers(array('member_id'=>$iMemberId))->first();
		if (empty($oMember)) {
			$this->session->error('Member not found.');
			redirect('members/admin_list');
		}

		if ($this->_isPost()) {
			$this->form_validation->set_rules($this->update_validation);
			$this->_current_user_email = $oMember->email;
			if ($this->form_validation->run()) {
				unset($this->_current_user_email);
				$oMember->email = set_value('email');
				$oMember->new_password = set_value('new_password');

				$oUpdate = Service::load('member')->updateMember($oMember);
				if ($oUpdate->isOk()) {
					$oMember = $oUpdate->first();
					$this->session->success('Member updated.');
					redirect('members/admin_list');
				} else {
					$this->session->current_error('Unable to member settings.');
				}
			} else {
				unset($this->_current_user_email);
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		$this->set('oMember',$oMember);
		
		$aAdminIds = $this->config->item('admin_ids');
		$this->set('aAdminIds',$aAdminIds);
		
		$subscription = $this->_getSubscriptionByMemberId($iMemberId);
		$trialMember = 0;
	    if ($subscription->status == SubscriptionModel::StatusTrial && $subscription->expire_date < date('Y-m-d H:i:s')) {
            $trialMember = 1;
	    }
	    if ($subscription->status == SubscriptionModel::StatusExpired) {
	        $trialMember = 1;
	    }
		$this->set('trialMember', $trialMember);
		
		$this->set('sPageTitle','Admin Member Edit');
		$this->set('aBreadCrumbs',array(
			array('title' => 'ADMIN')
			,array('title' => 'Members','path'=>'members/admin_list')
			,array('title' => 'Edit')
		));
		$this->build('members/admin_edit');
	}
	
	public function extend_trial() {
	    $memberId = isset($_POST['member_id']) && $_POST['member_id'] ? $_POST['member_id'] : null;
	    
	    $subscription = $this->_getSubscriptionByMemberId($memberId);
	    
	    if ($subscription) {
	        $subscription->plan_id = 0;
	        $subscription->contract_limit = 50;
	        $subscription->price = 0;
	        $subscription->status = SubscriptionModel::StatusTrial;
	        $subscription->expire_date = date('Y-m-d H:i:s', strtotime('+14 days'));
	        $subscription->next_billing_date = null;
	        $subscription->stripe_id = null;
	        
	        $oSS = Service::load('subscription');
	        $oSS->updateSubscription($subscription);
	    }
	    
	    echo json_encode(['status' => 1]);
	}

	public function test_password() {
		exit;
		// 8fa275823e615b1bcc778a364a0d1993
		$sPassword = 'WxjQXIAXuNLfo16pLiB9ff';
		$oMember = Service::load('member')->getMembers(array('member_id'=>199))->first();
		
		echo "<pre>";
		
		echo "should be ".Service::load('member')->asdfasdfasdf($oMember->email,$sPassword)."\n";
		if (!Service::load('member')->confirmPassword($oMember,$sPassword)) {
			echo "\nfailed check\n";
		} else {
			echo "\npassed check\n";
		}

		$oMember->new_password = $sPassword;
		$oUpdate = Service::load('member')->updateMember($oMember);
		
		$oMember = Service::load('member')->getMembers(array('member_id'=>199))->first();
		if (!Service::load('member')->confirmPassword($oMember,$sPassword)) {
			echo "\nfailed check2\n";
		} else {
			echo "\npassed check2\n";
		}
	}

	public function admin_delete($iMemberId) {
		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('members/login');
		}

		$oMember = Service::load('member')->getMembers(array('member_id'=>$iMemberId))->first();
		if (empty($oMember)) {
			$this->session->error('Member not found.');
			redirect('members/admin_list');
		}

		$aAdminIds = $this->config->item('admin_ids');
		if (in_array($oMember->member_id,$aAdminIds)) {
			$this->session->error('Not allowed. Member is admin.');
			redirect('members/admin_list');
		}

		$oDelete = Service::load('member')->deleteMember($oMember);
		if ($oDelete->isOk()) {
			$this->session->success('Member deleted.');
		} else {
			$this->session->error('Unable to delete member.');
		}

		redirect('members/admin_list');
	}

	///////////////////////////////////////////////////////////////////////////
	///  Login   /////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function login() {
		if (!empty($this->_iMemberId)) {
			redirect(ConfigService::getItem('default_redirect_to'));
		}

		$iMaxFailedAttempts = 5;
		if ($this->_isPost()) {
			$oMember = Service::load('member')->getMember(array(
				'email' => $this->input->my_post('email')
			))->reset();

			if (empty($oMember)) {
				$this->session->current_error('Email/Password combination not found.');

			} elseif ($oMember->status == MemberModel::StatusPending) {
				$this->session->current_error('You must confirm your account to login.');

			} elseif ($oMember->status != MemberModel::StatusActive) {
				$this->session->current_error('This account is unavailable. Please contact support.');

			} elseif ($oMember->count_failed_login_attempts >= $iMaxFailedAttempts) {
				log_message('error',$oMember->email.' failed login: account locked');
				$this->session->current_error('This account is locked. Please reset your password to continue.');

			} elseif (!Service::load('member')->confirmPassword($oMember,$this->input->my_post('password'))) {
				log_message('error',$oMember->email.' failed login: password');
				// log fail
				Service::load('member')->addFailedLoginAttempt($oMember->member_id);
				$oMember->count_failed_login_attempts += 1;
				Service::load('memberaccesslog')->addMemberAccessLog(new MemberAccessLogModel(array(
					'action_type' => MemberAccessLogModel::ACTION_TYPE_LOGIN_FAIL
					,'member_id' => $oMember->member_id
				)));
				if ($oMember->count_failed_login_attempts >= $iMaxFailedAttempts) {
					$this->_sendFailedAccessPasswordResetEmail($oMember);
					$this->session->current_error('This account is locked. Please reset your password to continue.');
					redirect('members/request_reset_password');
				}

				$this->session->current_error('Email/Password combination not found.');

			} else {
				log_message('error',$oMember->email.' success login');
				Service::load('member')->resetFailedLoginAttempt($oMember->member_id);
				$this->session->set_userdata('member_id',$oMember->member_id);
				$this->session->set_userdata('member_email',$oMember->email);
				$this->session->set_userdata('member_name',trim($oMember->first_name.' '.$oMember->last_name));
				$this->session->set_userdata('member_avatar',$oMember->avatar);
				$this->session->set_userdata('member_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
				$this->session->set_userdata('member_orig_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
				$this->session->set_userdata('member_create_date',$oMember->create_date);

				$oSub = $this->_getSubscriptionByMemberId($oMember->member_id);
				if ($oMember->status == MemberModel::StatusPending) {
					redirect('members/resend_confirmation_token/'.$oMember->member_id);

				} elseif ($oMember->status != MemberModel::StatusActive) {
					redirect('disabled_account');

				} elseif ($oMember->parent_id == $oMember->member_id) {
					if ($oSub->plan_id == 0 && $oSub->expire_date <= date('Y-m-d H:i:s')) {
						redirect('billing/trial_expired');
					} elseif (($oSub->contract_limit - 3) <= Service::load('contract')->getContractCount(array(
						'parent_id'=>$oMember->member_id
						,'status' => ContractModel::STATUS_ACTIVE
					))->total) {
						redirect('billing/near_limit');
					}

				} elseif ($oMember->parent_id != $oMember->member_id) {
					if ($oSub->plan_id == 0 && $oSub->expire_date <= date('Y-m-d H:i:s')) {
						redirect('billing/limited_access');

					} elseif (($oSub->contract_limit - 3) <= Service::load('contract')->getContractCount(array(
						'parent_id'=>$oMember->member_id
						,'status' => ContractModel::STATUS_ACTIVE

					))->total) {
						redirect('billing/limited_access');
					}
				}

				Service::load('memberaccesslog')->addMemberAccessLog(new MemberAccessLogModel(array(
					'action_type' => MemberAccessLogModel::ACTION_TYPE_LOGIN_SUCCESS
					,'member_id' => $oMember->member_id
				)));
				redirect(ConfigService::getItem('default_redirect_to'));
			}
		}

		$this->load->view('members/login_min',$this->aData);
	}

	public function logout() {
		$this->session->clear_by_key('admin_member_');
		$this->session->clear_by_key('member_');
		$this->session->unset_userdata('uploaded_contract_ids');
		$this->session->unset_userdata('member_current_subscription');
		$this->session->unset_userdata('member_subscription_last_load');
		redirect('members/login');
	}

	///////////////////////////////////////////////////////////////////////////
	///  Register   //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function request_reset_password() {
		if ($this->_isPost()) {
			$oGet = Service::load('member')->getMember(array(
				'email' => $this->input->my_post('email')
			));

			if (!$oGet->count) {
				$this->session->current_error('Email not found.');
			} else {
				$oMember = $oGet->first();
				$this->_sendPasswordResetEmail($oMember);
				$this->session->current_success('Reset instructions have been sent to your email.');
				$this->load->view('members/reset_password_check_email',$this->aData);
				return true;
			}
		}

		$this->load->view('members/request_reset_password',$this->aData);
	}

	public function test_failed_access_email() {
		$oGet = Service::load('member')->getMember(array(
				'member_id' => 1
		))->reset();
		$this->_sendFailedAccessPasswordResetEmail($oGet);
	}

	protected function _sendFailedAccessPasswordResetEmail(MemberModel $oMember) {
		log_message('required','password reset: '.$oMember->email.' '.$oMember->getPasswordResetToken());

		$sSubject = ConfigService::getItem('app_name').' - Reset Password';

		$sMessageHTML = $this->load->view('emails/failed_access_password_reset',array(
			'sResetLink' => site_url('members/confirm_reset_password_direct/'.$oMember->member_id.'?rsptk='.$oMember->getPasswordResetToken())
			,'sEmail'    => $oMember->email
		),true);

		$sMessageText = "There were multiple failed attempts to log in to your account, so as a security measure we're sending this email to reset your password. Please reset your password by clicking the button below.\n ".
			site_url('members/confirm_reset_password_direct/'.$oMember->member_id.'?rsptk='.$oMember->getPasswordResetToken());

		$bSent = HelperService::sendEmail($oMember->email,ConfigService::getItem('support_email'),$sSubject,$sMessageText,$sMessageHTML);
		log_message('required','reset email sent: '. $bSent?1:0);
		return $bSent;
	}

	protected function _sendPasswordResetEmail(MemberModel $oMember) {
		log_message('required','password reset: '.$oMember->email.' '.$oMember->getPasswordResetToken());

		$sSubject = ConfigService::getItem('app_name').' - Reset Password';

		$sMessageHTML = $this->load->view('emails/password_reset',array(
			'sResetLink' => site_url('members/confirm_reset_password_direct/'.$oMember->member_id.'?rsptk='.$oMember->getPasswordResetToken())
			,'sEmail'    => $oMember->email
		),true);

		$sMessageText = "We received a request to reset your password.\nIf you requested a password reset for {$oMember->email}, go to the url below below.\n ".
			site_url('members/confirm_reset_password_direct/'.$oMember->member_id.'?rsptk='.$oMember->getPasswordResetToken());

		$bSent = HelperService::sendEmail($oMember->email,ConfigService::getItem('support_email'),$sSubject,$sMessageText,$sMessageHTML);
		log_message('required','reset email sent: '. $bSent?1:0);
		return $bSent;
	}

	public function confirm_reset_password_direct($iMemberId) {
		$oMember = Service::load('member')->getMembers(array('member_id'=>$iMemberId))->first();
		if (empty($oMember)) {
			$this->session->error('Member not found.');
			redirect('members/request_reset_password');
		}

		if (empty($_GET['rsptk']) || strcmp($oMember->getPasswordResetToken(),$_GET['rsptk']) !== 0) {
			$this->session->error('Member not found. (Error: IT)');
			redirect('members/request_reset_password');
		}


		if ($this->_isPost()) {
			$this->form_validation->set_rules($this->password_reset_validation);
			if ($this->form_validation->run()) {
				$oMember->count_failed_login_attempts = 0;
				$oMember->new_password = set_value('password');
				$oUpdate = Service::load('member')->updateMember($oMember);
				if ($oUpdate->isOk()) {
					Service::load('memberaccesslog')->addMemberAccessLog(new MemberAccessLogModel(array(
						'action_type' => MemberAccessLogModel::ACTION_TYPE_PASSWORD_RESET
						,'member_id' => $oMember->member_id
					)));
					Service::load('memberaccesslog')->addMemberAccessLog(new MemberAccessLogModel(array(
						'action_type' => MemberAccessLogModel::ACTION_TYPE_LOGIN_SUCCESS
						,'member_id' => $oMember->member_id
					)));

					$this->session->success('Password updated.');

					$this->session->set_userdata('member_id',$oMember->member_id);
					$this->session->set_userdata('member_email',$oMember->email);
					$this->session->set_userdata('member_name',trim($oMember->first_name.' '.$oMember->last_name));
					$this->session->set_userdata('member_avatar',$oMember->avatar);
					$this->session->set_userdata('member_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
					$this->session->set_userdata('member_orig_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
					$this->session->set_userdata('member_create_date',$oMember->create_date);

					redirect('welcome');
				} else {
					$this->session->current_error('Unable to update password');
				}
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		$this->aData['iMemberId'] = $oMember->member_id;
		$this->aData['rsptk'] = $_GET['rsptk'];
		$this->aData['sEmail'] = $oMember->email;
		$this->load->view('members/confirm_reset_password_direct',$this->aData);
	}

	public function confirm_reset_password($iMemberId) {
		$oMember = $this->_getMember($iMemberId);

		if ($this->_isPost()) {
			$sToken = $this->input->my_post('rsptk');
			$sActualToken = $oMember->getPasswordResetToken();
			if (strcmp($sActualToken,$sToken)===0) {
				// stuff
				$this->session->set_userdata('reset_password_token',$sActualToken);
				redirect('members/reset_password/'.$iMemberId);
			} else {
				$this->session->current_error('Invalid token');
			}
		}

		$this->aData['iMemberId'] = $iMemberId;
		$this->load->view('members/confirm_reset_password',$this->aData);
	}

	public function reset_password($iMemberId) {
		$oMember = $this->_getMember($iMemberId);

		$sToken = $this->session->userdata('reset_password_token');
		$sActualToken = $oMember->getPasswordResetToken();
		if (strcmp($sActualToken,$sToken)!==0) {
			$this->session->unset_userdata('reset_password_token');
			$this->session->error('Email mismatch.');
			redirect('members/request_reset_password');
		}

		if ($this->_isPost()) {
			$this->form_validation->set_rules($this->password_reset_validation);
			if ($this->form_validation->run()) {
				$oMember->count_failed_login_attempts = 0;
				$oMember->new_password = set_value('password');
				$oUpdate = Service::load('member')->updateMember($oMember);
				if ($oUpdate->isOk()) {
					Service::load('memberaccesslog')->addMemberAccessLog(new MemberAccessLogModel(array(
						'action_type' => MemberAccessLogModel::ACTION_TYPE_PASSWORD_RESET
						,'member_id' => $oMember->member_id
					)));
					$this->session->unset_userdata('reset_password_token');
					$this->session->success('Password updated.');
					redirect('members/login');
				} else {
					$this->session->current_error('Unable to update password');
				}
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		}

		$this->aData['iMemberId'] = $iMemberId;
		$this->load->view('members/reset_password',$this->aData);
	}

	///////////////////////////////////////////////////////////////////////////
	///  Register   //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Register
	 *
	 * @access public
	 * @return void
	 */
	public function register() {
		/*if (empty($_GET['override_lockout']) || $_SERVER['override_lockout'] != '8675309') {
			$this->session->error('Signups are currently closed. Please try again later.');
			redirect('members/login');
			return;
		}*/

		if (!empty($this->_iMemberId)) {
			redirect(ConfigService::getItem('default_redirect_to'));
		}

		if ($this->_isPost()) {
		    $this->form_validation->set_rules($this->signup_validation);
		    $this->form_validation->set_rules('recaptcha', 'Captcha', 'required|callback_check_recaptcha');
		    
			if ($this->form_validation->run()) {
				$oMember = new MemberModel();
				$oMember->create_date = date('Y-m-d H:i:s');

				foreach ($this->signup_validation as $aRule) {
					$oMember->setField($aRule['field'], set_value($aRule['field']));
				}
				
				if (isset($_POST['country_code']) && $country_code = $_POST['country_code']) {
				    $country = Service::load('country')->getCountry(['status' => 1, 'country_code' => strtoupper($country_code)])->first();
				    if ($country) {
				        $oMember->country_id = $country->id;
				        $oMember->currency = $country->currency;
				    }
				}
				if (!$oMember->country_id) {
				    $country = Service::load('country')->getCountry(['status' => 1, 'default_country' => 1])->first();
				    if ($country) {
				        $oMember->country_id = $country->id;
				        $oMember->currency = $country->currency;
				    }
				}
				
				$oAddResponse = Service::load('member')->addMember($oMember);
				if ($oAddResponse->isOk()) {
					$oMember = $oAddResponse->first();
					$oMember->parent_id = $oMember->member_id;
					Service::load('member')->updateMember($oMember);

					//Convert lead if exists when they complete registration
					try {
						$this->load->library('Intercom');
						$leads = $this->intercom->getLeads(['email' => $oMember->email]);
						if ($leads->total_count > 0) {
							foreach($leads->contacts as $lead) {
								$this->intercom->convertLead($lead->user_id, $oMember->email);
							}
						}
					} catch (Exception $e) {
						log_message('error','unable to add to intercom: '.$oMember->email);
					}
					
					send_analytic_event('Signed Up', $oMember, null);
                    
					$this->_sendConfirmation($oMember);
					$this->load->view('members/confirm_activation', $this->aData);
					return;
				}
			} else {
				$this->session->current_error($this->form_validation->first_error());
			}
		} elseif (!empty($_GET['e']) && filter_var($_GET['e'],FILTER_VALIDATE_EMAIL)) {
			$this->set('sEmail',$_GET['e']);

			try {
				$this->_createLead($_GET['e']);
			} catch (Exception $e) {
				log_message('error','unable to create lead in intercom: '.$_GET['e']);
			}
		}

		$this->load->view('members/register',$this->aData);
	}
	
	public function check_recaptcha($recaptcha)
	{
	    if (!$recaptcha) {
	        $this->form_validation->set_message('check_recaptcha', 'Google Recaptcha is required.');
	        return FALSE;
	    }
	    
	    $secretKey = $_ENV['RECAPTCHA_SECRET_KEY'];
	    
	    // post request to server
	    $context  = stream_context_create([
	        'http' => [
	            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	            'method'  => 'POST',
	            'content' => http_build_query(['secret' => $secretKey, 'response' => $recaptcha])
	        ]
	    ]);
	    $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
	    $responseKeys = json_decode($response, true);

	    if ($responseKeys["success"]) {
	        if ($responseKeys["score"] >= 0.5) {
	           return TRUE;
	        } else {
	            $this->form_validation->set_message('check_recaptcha', 'Failed Google reCAPTCHA check. Please email sales@contracthound.com for further support.');
	            return FALSE;
	        }
	    } else {
	        $this->form_validation->set_message('check_recaptcha', 'Google Recaptcha is not valid.');
	        return FALSE;
	    }
	}

	public function confirm_activation() {
		$this->load->view('members/confirm_activation', $this->aData);
		return;
	}

	public function register_later() {
		$this->build('members/register_later');
	}

	/**
	 * Email Confirmation
	 *
	 * @access protected
	 * @param MemberModel $oMember
	 * @return boolean
	 */
	protected function _sendConfirmation(MemberModel $oMember) {
		log_message('required','confirmation: '.$oMember->email.' '.$oMember->getEmailConfirmationToken());

		$sToken = $oMember->getEmailConfirmationToken();
		$sUrl = site_url('members/confirm/'.$oMember->member_id).'?cfmtk='.$sToken;

		$sSubject = ConfigService::getItem('app_name').' - Confirm your email';

		$sMessageHTML = $this->load->view('emails/confirm_email',array(
			'sConfirmationLink' => $sUrl
			,'sEmail'    => $oMember->email
		),true);

		$sMessageText = $this->lang->line('member_confirm_email_text');

		$bSent = HelperService::sendEmail(
			$oMember->email,
			ConfigService::getItem('support_email'),
			$sSubject,
			$sMessageText,
			$sMessageHTML
		);
		log_message('required','confirmation email sent: '. ($bSent?1:0));
		return $bSent;
	}

	public function test_confirmation_email() {
		$oGet = Service::load('member')->getMember(array(
				'member_id' => 2
		))->reset();
		$this->_sendConfirmation($oGet);
	}

	/**
	 * Create Intercom Lead
	 *
	 * @param $sEmail
	 */
	protected function _createLead($sEmail) {
		log_message('required', "create lead / $sEmail");

		$this->load->library('intercom');
		$this->intercom->createLead(['email' => $sEmail]);
	}

	/**
	 * Confirm Email
	 *
	 * @access public
	 * @param integer $iMemberId
	 * @return void
	 */
	public function confirm($iMemberId = "") {
		if (empty($iMemberId) || !is_numeric($iMemberId)) {
			//redirect('members/register');
			$this->load->view('members/confirm_activation', $this->aData);
			return;
		}

		$oMember =$this->_getMember($iMemberId);
		if ($oMember->status != MemberModel::StatusPending) {
			redirect('members/login');
		}

		$sConfirmationToken = trim($this->input->my_get_post('cfmtk'));
		if (!empty($sConfirmationToken)) {
			$sActualToken = $oMember->getEmailConfirmationToken();
			if (strcmp($sActualToken,$sConfirmationToken)===0) {
				$oMember->status = MemberModel::StatusActive;

				$oUpdate = Service::load('member')->updateMember($oMember);
				if ($oUpdate->isOk()) {

					$this->session->success('Confirmation Successful!');

					$this->session->set_userdata('member_id',$oMember->member_id);
					$this->session->set_userdata('member_email',$oMember->email);
					$this->session->set_userdata('member_name',trim($oMember->first_name.' '.$oMember->last_name));
					$this->session->set_userdata('member_avatar',$oMember->avatar);
					$this->session->set_userdata('member_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
					$this->session->set_userdata('member_orig_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
					$this->session->set_userdata('member_create_date',$oMember->create_date);
					$this->_getSubscription();
					
					send_analytic_event('Email Confirmed', $oMember, null);
					$this->session->set_userdata('email_confirmed', 'email_confirmed');
					
					redirect('welcome');
				} else {
					$this->session->current_error('Unknown error. Please try again.');
				}
			} else {
				$this->session->current_error('Invalid confirmation code.');
			}
		}

		$this->aData['iMemberId'] = $iMemberId;
		$this->build('members/confirm');
	}

	/**
	 * Resend Confirmation Token
	 *
	 * @access public
	 * @return void
	 */
	public function resend_confirmation_token($iMemberId=null) {
		if (!empty($iMemberId)) {
			$oMember = Service::load('member')->getMember(array(
				'member_id' => $iMemberId
			))->first();

			if (!empty($oMember) && $oMember->status == MemberModel::StatusPending) {
				$this->_sendConfirmation($oMember);
				$this->load->view('members/confirm_activation', $this->aData);
				return;
			}
		}
		if ($this->_isPost()) {
			$oMember = Service::load('member')->getMember(array(
				'email' => $this->input->my_post('email')
			))->first();

			if (empty($oMember)) {
				$this->session->current_error('Email not found.');
			} else {
				if ($oMember->status != MemberModel::StatusPending) {
					redirect('members/login');
				}

				$this->_sendConfirmation($oMember);
				$this->load->view('members/confirm_activation', $this->aData);
				return;
			}
		}

		$this->load->view('members/resend_confirmation_email',$this->aData);
	}

	/**
	 * Registers a new subaccount
	 * allows creation of new password
	 *
	 * @param string $id
	 * @throws Exception
	 */
	public function register_subaccount($iMemberId = "") {
		if (empty($iMemberId) || !is_numeric($iMemberId)) {
			//redirect('members/register');
			$this->load->view('members/confirm_activation', $this->aData);
			return;
		}

		$oMember =$this->_getMember($iMemberId);
		if ($oMember->status != MemberModel::StatusPending) {
			redirect('members/login');
		}

		$sConfirmationToken = trim($this->input->my_get_post('cfmtk'));

		if($this->_isPost()) {
			$this->form_validation->set_rules($this->accept_subaccount_validation);
			if ($this->form_validation->run()) {
				if (!empty($sConfirmationToken)) {
					$sActualToken = $oMember->getEmailConfirmationToken();

					if (strcmp($sActualToken, $sConfirmationToken) === 0) {
						$oMember->status = MemberModel::StatusActive;
						$oMember->new_password = set_value('password');
						$oUpdate = Service::load('member')->updateMember($oMember);

						if ($oUpdate->isOk()) {

							$this->session->success('Confirmation Successful!');

							$this->session->set_userdata('member_id',$oMember->member_id);
							$this->session->set_userdata('member_email',$oMember->email);
							$this->session->set_userdata('member_name',trim($oMember->first_name.' '.$oMember->last_name));
							$this->session->set_userdata('member_avatar',$oMember->avatar);
							$this->session->set_userdata('member_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
							$this->session->set_userdata('member_orig_parent_id',$oMember->parent_id?$oMember->parent_id:$oMember->member_id);
							$this->session->set_userdata('member_create_date',$oMember->create_date);
							
							send_analytic_event('User Invitation Accepted', $oMember, null);

							redirect('welcome');
						} else {
							$this->session->current_error('Unknown error. Please try again.');
							log_message('error','::register_subaccount '.$iMemberId.' update failed');
						}
					} else {
						$this->session->current_error('Invalid confirmation code.');
						log_message('error','::register_subaccount '.$iMemberId.' Invalid confirmation code');
					}
				}

			} else {
				$this->session->current_error($this->form_validation->first_error());
				log_message('error','::register_subaccount '.$iMemberId.' validation failed '.$this->form_validation->first_error());
			}
		}

		$oParentMember = Service::load('member')->getParentMember($oMember)->reset();

		$this->aData['iMemberId'] = $iMemberId;
		$this->aData['sMemberEmail'] = $oMember->email;
		$this->aData['sConfirmationToken'] = $sConfirmationToken;
		$this->aData['sTeamName'] = "$oParentMember->first_name $oParentMember->last_name";
		$this->load->view('members/accept_subaccount',$this->aData);
	}

	///////////////////////////////////////////////////////////////////////////
	/////  Cron Methods   ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Clear Old Log Files
	 *
	 * @access public
	 * @return void
	 */
	public function cron_clear_old_logs() {
		$aFiles = glob('application/logs/log-*.php');
		$sBaseName = 'log-'.date('Y-m-d',strtotime('-7 days')).'.php';
		//echo 'Base: '.$sBaseName."\n";

		foreach ($aFiles as $sFile) {
			if (strcmp(basename($sFile),$sBaseName) < 0) {
				//echo 'Old: '.$sFile."\n";
				@unlink($sFile);
			}
		}
	}

	///////////////////////////////////////////////////////////////////////////
	/////  Helper Methods   //////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function view_log($bCleanLog=false) {
		$sFilePath = 'application/logs/log-'.date('Y-m-d').'.php';

		if (file_exists($sFilePath)) {
			echo "<pre>\n".str_replace(array('<','>'),array('&lt;','&gt;'),file_get_contents($sFilePath));

			if ($bCleanLog) {
				@unlink($sFilePath);
			}
		} else {
			echo 'log file not found';
		}
	}

	public function view_session() {
		echo '<pre>'; var_dump($_SESSION);
		/*if (!empty($_SESSION)) {
			foreach ($_SESSION as $sKey=>$sValue) {
				$mValue = @unserialize($sValue);
				if (!empty($mValue)) {
					var_dump($sKey,$mValue);
				} else {
					var_dump($sKey,$sValue);
				}
			}
		}*/
	}

	/**
	 * Get Member
	 *
	 * @access protected
	 * @param integer $iMemberId
	 * @return MemberModel
	 */
	protected function _getMember($iMemberId) {
		if (empty($iMemberId) || !is_numeric($iMemberId)) {
			$this->session->error('Invalid member');
			redirect('member/register');
		}

		$oResponse = Service::load('member')->getMember(array(
			'member_id' => $iMemberId
		));

		if (!$oResponse->count) {
			$this->session->error('Member not found.');
			redirect('members/logout');
		}

		return $oResponse->first();
	}

	/**
	 * Check email for existing user
	 *
	 * @access protected
	 * @param string $sEmail
	 * @return boolean
	 */
	public function _check_email($sEmail) {
	    list($user, $domain) = explode('@', $sEmail);
	    if ($domain == 'gmail.com' || $domain == 'live.com' || $domain == 'hotmail.com' || $domain == 'yahoo.com'|| $domain == 'yahoo.co'|| $domain == 'aol.com') {
	        $this->form_validation->set_message('_check_email', $this->lang->line('business_email_required'));
	        return false;
	    }
	    
		if ($this->_iMemberId) {
			$oMember = $this->_getMember($this->_iMemberId);
			if (strcmp($sEmail,$oMember->email) === 0) {
				return true;
			}
		}

		if (!empty($this->_current_user_email) && strcmp($sEmail,$this->_current_user_email) === 0) {
			return true;
		}

		$oExistingUserResponse = Service::load('member')->getMember(array(
			'email' => $sEmail
		));

		if ($oExistingUserResponse->size() == 0) {
			return true;
		} else {
			$this->form_validation->set_message('_check_email', $this->lang->line('member_email_exists'));
			return false;
		}
	}

	/**
	 * Password Required Check
	 *
	 * @access public
	 * @param string $sEmail
	 * @return boolean
	 */
	public function _password_required_new_email($sEmail) {
		$oMember = $this->_getMember($this->_iMemberId);
		if (strcmp($sEmail,$oMember->email) === 0) {
			return true;
		}

		$sPassword = $this->input->my_post('new_password');
		if (empty($sPassword)) {
			$this->form_validation->set_message('_password_required_new_email', $this->lang->line('member_email_requires_password'));
			return false;
		}

		return true;
	}

	/**
	 * Check Current Password
	 *
	 * @access public
	 * @param string $sCurrentPassword
	 * @return boolean
	 */
	public function _check_current_password($sCurrentPassword) {
		if (empty($this->_iMemberId)) {
			$this->form_validation->set_message('_check_current_password', $this->lang->line('member_incorrect_current_password'));
			return false;
		}

		$oMemberService = Service::load('member');
		$oMember = $oMemberService->getMember(array(
			'member_id' => $this->_iMemberId
		))->first();

		if (empty($oMember)) {
			$this->form_validation->set_message('_check_current_password', $this->lang->line('member_incorrect_current_password'));
			return false;
		}

		if (!$oMemberService->confirmPassword($oMember,$sCurrentPassword)) {
			$this->form_validation->set_message('_check_current_password', $this->lang->line('member_incorrect_current_password'));
			return false;
		}

		return true;
	}

	/**
	 * Check confirm email
	 *
	 * @access protected
	 * @param string $sConfirmEmail
	 * @return boolean
	 */
	public function _check_confirm_email($sConfirmEmail) {
		if (empty($sConfirmEmail)) {
			return true;
		} else {
			$this->form_validation->set_message('_check_confirm_email', 'Unknown error mb1');
			return false;
		}
	}

	/**
	 * Check confirm password
	 *
	 * @access protected
	 * @param string $sConfirmPassword
	 * @return boolean
	 */
	public function _check_confirm_password($sConfirmPassword) {
		if (empty($sConfirmPassword)) {
			return true;
		} else {
			$this->form_validation->set_message('_check_confirm_password', 'Unknown error mb2');
			return false;
		}
	}}
