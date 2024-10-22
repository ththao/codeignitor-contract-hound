<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Welcome extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

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
	 * Welcome once logged in
	 *
	 * @access public
	 */
	public function index() {
	    $oReminders = Service::load('reminder')->getRemindersByTeamMember($this->_iMemberId,$this->_iParentId,1);
	    $eReminders = Service::load('reminder')->getRemindersByTeamMember($this->_iMemberId,$this->_iParentId,0);
	    
		if ($this->_iMemberId) {
			$aApprovals = Service::load('contractapproval')->getContractApprovals(array(
				'member_id' => $this->_iMemberId
				,'status'   => ContractApprovalModel::STATUS_PENDING
			),'step asc, member_id asc',50)->results();

			if (!empty($aApprovals)) {
				$aContractIds = array();
				foreach ($aApprovals as $oApproval) {
					$aContractIds[] = $oApproval->contract_id;
				}

				$oContracts = Service::load('contract')->getContracts(array('contract_id'=>$aContractIds,'parent_id'=>$this->_iParentId));

				foreach ($aApprovals as $iPos=>$oApproval) {
					$bMatchingContractFound = false;
					foreach ($oContracts as $oContract) {
						if ($oContract->contract_id == $oApproval->contract_id) {
							$oApproval->contract = $oContract;
							$bMatchingContractFound = true;
						}
					}

					$aApprovals[$iPos] = $oApproval;
					if (!$bMatchingContractFound) {
						unset($aApprovals[$iPos]);
					}
				}
			}
			$this->set('aApprovals',$aApprovals);
		}

		$this->session->set_userdata('member_last_page','/welcome');

		$oDocusignContracts = Service::load('docusigncontract')->getDocusignContractsForAccount(
			$this->_iParentId
			,DocusignContractModel::STATUS_PENDING
		);
		$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array(
			'parent_id' => $this->_iParentId
			,'status'   => DocusignAccessTokenModel::STATUS_ACTIVE
		))->reset();
		if (empty($oToken) && $oDocusignContracts->count) {
			$this->session->current_error($this->lang->line('docusign_expired_token_notification'));
			log_message('error','token expired');
		}
		
		$this->set('sHeader','Welcome Page');
		$this->set('oReminders',$oReminders);
		$this->set('eReminders',$eReminders);
		$this->set('bIsDashboard',true);
		$this->set('bIsPaidSub', $this->_getSubscription()->status != SubscriptionModel::StatusTrial);
		$this->build('welcome/has_contracts');
	}

	public function dismiss_trial_notification() {
		$this->session->set_userdata('member_hide_trial_notification',true);
		echo json_encode(array(
			'success' => 1
		));
		return true;
	}

	public function clear_dismiss() {
		$this->session->unset_userdata('member_hide_trial_notification');
		redirect('/welcome');
	}
}