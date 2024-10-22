<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Approvals extends User_Controller {

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
		redirect('welcome');
	}

	/*public function test_send_approval_notification() {
		$oMemberToNotify = Service::load('member')->getMembers(array('member_id'=>1))->reset();
		$oContract = Service::load('contract')->getContracts(array('contract_id'=>79))->reset();
		$mResponse = $this->_sendApprovalNotification($oMemberToNotify,$oContract);
		
		echo '<pre>'; var_dump($mResponse);
		return true;
	}*/

	protected function _sendApprovalNotification($oMemberToNotify,$oContract,$bReminder=false) {
		$sSubject = $this->lang->line('contract_approval_notify_subject_'.($bReminder?'reminder_':'').'text');
		
		$oUploader = Service::load('member')->getMembers(array('member_id'=>$oContract->owner_id))->reset();

		$sMessageHTML = $this->load->view('emails/approval_notify',array(
			'iContractId'    => $oContract->contract_id
			,'sUploaderName' => $oUploader->name?$oUploader->name:$oUploader->email
			,'sDate'         => convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%x')//date('n/j/Y',strtotime($oContract->last_updated))
			,'sFilename'     => $oContract->file_name
		),true);

		$sMessageText = $this->lang->line('contract_approval_notify_message_text');
		$sMessageText = str_replace('%%CONTRACT_ID%%',$oContract->contract_id,$sMessageText);
		$sMessageText = str_replace('%%UPLOADER_NAME%%',$oUploader->name?$oUploader->name:$oUploader->email,$sMessageText);
		$sMessageText = str_replace('%%UPLOAD_DATE%%',/*date('n/j/Y',strtotime($oContract->last_updated))*/convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%x'),$sMessageText);
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

	public function send_approval_reminder($iContractApprovalId) {
		$oStep = Service::load('contractapproval')->getContractApprovals(array(
			'contract_approval_id' => $iContractApprovalId
		))->reset();

		if (empty($oStep)) {
			$this->session->error('Approval step not found.');
			redirect($this->_getLastPage());
		}
		
		$oMemberToNotify = Service::load('member')->getMembers(array('member_id'=>$oStep->member_id))->reset();
		$oContract = Service::load('contract')->getContracts(array('contract_id'=>$oStep->contract_id))->reset();
		$this->_sendApprovalNotification($oMemberToNotify,$oContract,true);

		$this->session->success('Approval reminder sent.');
		redirect($this->_getLastPage());
	}

	public function approve_step($iContractApprovalId) {
		$oCAS = Service::load('contractapproval');
		$oStep = $oCAS->getContractApprovals(array(
			'contract_approval_id' => $iContractApprovalId
		))->reset();

		if (empty($oStep)) {
			$this->session->error('Approval step not found.');
			redirect($this->_getLastPage());
		}
		
		if ($this->_iMemberId != $oStep->member_id) {
			$this->session->error('You are not the owner of this approval step.');
			redirect($this->_getLastPage());
		}
		
		$oStep->status = ContractApprovalModel::STATUS_APPROVED;
		$oCAS->updateContractApproval($oStep);
		Service::load('contractlog')->addContractLog(new ContractLogModel(array(
			'contract_id'  => $oStep->contract_id
			,'member_id'   => $this->_iMemberId
			,'message'     => ''
			,'type'        => ContractLogModel::TYPE_APPROVED
			,'create_date' => date('Y-m-d H:i:s')
		)));
		
		$bSetActiveNextStep = false;
		if ($oStep->type == ContractApprovalModel::TYPE_ANY) {
			$bSetActiveNextStep = true;
			//log_message('required','::approve_step TYPE_ANY');

			//$_SESSION['debug_sql'] = 1;
			$oCAS->updateContractApprovals(array(
				'contract_approval_id !=' => $iContractApprovalId
				,'contract_id'            => $oStep->contract_id
				,'step'                   => $oStep->step
				,'status'                 => ContractApprovalModel::STATUS_PENDING
			),array(
				'status' => ContractApprovalModel::STATUS_SKIPPED
			));
		} else {
			$oOtherCurrentSteps = $oCAS->getContractApprovals(array(
				'contract_approval_id !=' => $iContractApprovalId
				,'contract_id'            => $oStep->contract_id
				,'step'                   => $oStep->step
				,'status'                 => ContractApprovalModel::STATUS_PENDING
			));

			//log_message('required','::approve_step $oOtherCurrentSteps '.print_r($oOtherCurrentSteps,true));
			if (count($oOtherCurrentSteps) == 0) {
				$bSetActiveNextStep = true;
			}
		}

		if ($bSetActiveNextStep) {
			$oNextSteps = $oCAS->getContractApprovals(array(
				'contract_id' => $oStep->contract_id
				,'status !='  => ContractApprovalModel::STATUS_APPROVED
			));
			
			if (count($oNextSteps) == 0) {
				Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'contract_id'  => $oStep->contract_id
					,'member_id'   => 0
					,'message'     => ''
					,'type'        => ContractLogModel::TYPE_FULLY_APPROVED
					,'create_date' => date('Y-m-d H:i:s')
				)));

				// check docusign
				if (Service::load('contractsignature')->getContractSignatureCount(array(
					'contract_id' => $oStep->contract_id
				))->total) {
					Service::load('docusigncontract')->addDocusignContract(new DocusignContractModel(array(
						'contract_id'  => $oStep->contract_id
						,'status'      => DocusignContractModel::STATUS_PENDING
						,'create_date' => date('Y-m-d H:i:s')
					)));
				}

				redirect('contracts/view/'.$oStep->contract_id);
			}
		}

		if ($bSetActiveNextStep) {
			$oOtherCurrentSteps = $oCAS->updateContractApprovals(array(
				'contract_id' => $oStep->contract_id
				,'step'       => $oStep->step + 1
			),array(
				'status' => ContractApprovalModel::STATUS_PENDING
			));
			
			// get steps and alert them
			$oNextSteps = $oCAS->getContractApprovals(array(
				'contract_approval_id !=' => $iContractApprovalId
				,'contract_id'            => $oStep->contract_id
				,'status'                 => ContractApprovalModel::STATUS_PENDING
			));
			
			foreach ($oNextSteps as $oNextStep) {
				$oContract = Service::load('contract')->getContracts(array('contract_id'=>$oNextStep->contract_id))->reset();
				$oMemberToNotify = Service::load('member')->getMember(array('member_id'=>$oNextStep->member_id))->reset();
				$this->_sendApprovalNotification($oMemberToNotify,$oContract);
			}
		}

		redirect('contracts/view/'.$oStep->contract_id);
	}

	public function reject_step($iContractApprovalId) {
		$oStep = Service::load('contractapproval')->getContractApprovals(array(
			'contract_approval_id' => $iContractApprovalId
		))->reset();

		if (empty($oStep)) {
			$this->session->error('Approval step not found.');
			redirect($this->_getLastPage());
		}

		$oContract = Service::load('contract')->getContracts(array('contract_id'=>$oStep->contract_id))->reset();
		$oContractOwner = Service::load('member')->getMember(array('member_id'=>$oContract->owner_id))->reset();
		$this->_sendRejectionNotification($oContractOwner,$oContract);

		$oStep->status = ContractApprovalModel::STATUS_REJECTED;
		Service::load('contractapproval')->updateContractApproval($oStep);
		Service::load('contractlog')->addContractLog(new ContractLogModel(array(
			'contract_id'  => $oStep->contract_id
			,'member_id'   => $this->_iMemberId
			,'message'     => ''
			,'type'        => ContractLogModel::TYPE_REJECTED
			,'create_date' => date('Y-m-d H:i:s')
		)));
		redirect('contracts/view/'.$oStep->contract_id);
	}

	/*public function test_reject_notify() {
		$oStep = Service::load('contractapproval')->getContractApprovals(array(
			'contract_approval_id' => 24
		))->reset();
		$oContract = Service::load('contract')->getContracts(array('contract_id'=>$oStep->contract_id))->reset();
		$oContractOwner = Service::load('member')->getMember(array('member_id'=>$oContract->owner_id))->reset();
		$this->_sendRejectionNotification($oContractOwner,$oContract);
	}*/

	protected function _sendRejectionNotification($oMemberToNotify,$oContract) {
		$sSubject = $this->lang->line('contract_reject_notify_subject_text');
		
		$oUploader = Service::load('member')->getMembers(array('member_id'=>$oContract->owner_id))->reset();
		$sRejectorName = !empty($_SESSION['member_name'])?$_SESSION['member_name']:$_SESSION['member_email'];

		$sMessageHTML = $this->load->view('emails/reject_notify',array(
			'iContractId'    => $oContract->contract_id
			,'sUploaderName' => $oUploader->name?$oUploader->name:$oUploader->email
			,'sDate'         => convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%x')//date('n/j/Y',strtotime($oContract->last_updated))
			,'sFilename'     => $oContract->file_name
			,'sRejectorName' => $sRejectorName
		),true);

		$sMessageText = $this->lang->line('contract_reject_notify_message_text');
		$sMessageText = str_replace('%%CONTRACT_ID%%',$oContract->contract_id,$sMessageText);
		$sMessageText = str_replace('%%UPLOADER_NAME%%',$oUploader->name?$oUploader->name:$oUploader->email,$sMessageText);
		$sMessageText = str_replace('%%UPLOAD_DATE%%',/*date('n/j/Y',strtotime($oContract->last_updated))*/convertto_local_datetime($oContract->last_updated,$this->cTimeZone,'%m'),$sMessageText);
		$sMessageText = str_replace('%%FILENAME%%',$oContract->file_name,$sMessageText);
		$sMessageText = str_replace('%%URLTOCONTRACT%%',site_url('/contracts/view/'.$oContract->contract_id),$sMessageText);
		$sMessageText = str_replace('%%REJECTORNAME%%',$sRejectorName,$sMessageText);
		
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
}