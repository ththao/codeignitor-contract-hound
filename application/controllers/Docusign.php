<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// https://docs.docusign.com/esign/guide/authentication/oa2_auth_code.html
class Docusign extends User_Controller {

	//protected $sDocuSignBase = 'account-d.docusign.com'; // for the developer sandbox
	protected $sDocuSignBase = 'account.docusign.com'; // for the production platform.

	///////////////////////////////////////////////////////////////////////////
	///  Super Methods   /////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function __construct() {
		parent::__construct();

		$this->load->library('DocusignAPI');
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
		$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array(
			'parent_id' => $this->_iParentId
		))->reset();
		$this->set('sHeader','DocuSign Settings');
		$this->set('oToken',$oToken);
		$this->build('integrations/docusign');
	}

	public function disconnect() {
		$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array(
			'parent_id' => $this->_iParentId
		))->reset();
		
		if (empty($oToken)) {
			$this->session->error('No DocuSign token found.');
			redirect('docusign');
		}

		Service::load('docusignaccesstoken')->deleteDocusignAccessToken(array('docusign_access_token_id'=>$oToken->docusign_access_token_id));
		redirect('docusign');
	}

	/*  call too
		GET /oauth/auth?
			response_type=code
			&scope=signature
			&client_id=230546a7-9c55-40ad-8fbf-af205d5494ad  // integration key
			&state=a39fh23hnf23 // rand code from use
			&redirect_uri=https://app.contracthound.com/callback/
	*/
	public function connect_account() {
		$sCode = md5($this->_iParent_id.'D0cuS1gns4lt'.$this->_iMemberId);
		$this->session->set_userdata('member_docusign_state_code',$sCode);
		$sUrl = "https://{$this->sDocuSignBase}/oauth/auth?".
			'response_type=code'.
			'&scope=signature'.
			'&client_id='.ConfigService::getItem('docusign_integration_key').
			'&state='.$sCode. // rand code from use
			'&redirect_uri='.urlencode('https://app.contracthound.com/docusign/callback');
		redirect($sUrl);
	}

	/* callback
		HTTP/1.1 302 Found
		Location: https://app.contracthound.com/callback?
			// if error
			error=invalid_request
			&error_description=Unsupported%20request.

			// if successfull
			code=ey2dj3nd.AAAA39djasd3.dkn4449d21d
			&state=a39fh23hnf23
	*/
	public function callback() {
		//log_message('required','Docusign callback get: '.print_r($_GET,true));
		//log_message('required','Docusign callback post: '.print_r($_POST,true));
		//log_message('required','Docusign callback server: '.print_r($_SERVER,true));

		// TODO Build state code check
		$sStateCode = $this->session->userdata('member_docusign_state_code');

		if (!empty($_GET['code'])) {
			if ($this->getToken($_GET['code'])) {
				$this->session->unset_userdata('member_docusign_state_code');
				$this->session->success('DocuSign Connected.');
				redirect('integrations');
				return true;
			}
		}

		$this->session->unset_userdata('member_docusign_state_code');
		$this->session->success('Unable to signin to DocuSign.');
		redirect('integrations');
	}

	/*
		For example, if your integrator key is 230546a7-9c55-40ad-8fbf-af205d5494ad and the secret key 
			is 3087555e-0a1c-4aa8-b326-682c7bf276e9 you can get the base64 value like this in a JavaScript console:
		
		base64_encode('230546a7-9c55-40ad-8fbf-af205d5494ad:3087555e-0a1c-4aa8-b326-682c7bf276e9')
		"MjMwNTQ2YTctOWM1NS00MGFkLThmYmYtYWYyMDVkNTQ5NGFkOjMwODc1NTVlLTBhMWMtNGFhOC1iMzI2LTY4MmM3YmYyNzZlOQ=="
	*/

	/*
		POST /oauth/token
		Content-Type: application/x-www-form-urlencoded
		Authorization: Basic MjMwNTQ2YTctOWM1NS00MGFkLThmYmYtYWYyMDVkNTQ5NGFkOjMwODc1NTVlLTBhMWMtNGFhOC1iMzI2LTY4MmM3YmYyNzZlOQ==
		
		grant_type=authorization_code&code=ey2dj3nd.AAAA39djasd3.dkn4449d21d

		{
		  "access_token" : "eyA3J3ad.k32jdskd.ann4ds"  // The token you will use in the Authorization header of calls to the DocuSign API.
		  "token_type" : "Bearer",  // This is the kind of token. It is usually Bearer
		  "refresh_token" : "ey4fdd3nd.AAAA3d2ddagq3.akd1243d31d",  // A token you can use to get a new access_token without requiring user interaction.
		  "expires_in" : 57592   // The number of seconds before the access_token expires.
		}
	*/
	protected function getToken($sCode) {
		$aResponse = $this->docusignapi->postUrlContents(
			"https://{$this->sDocuSignBase}/oauth/token"
			,array(
				'grant_type' => 'authorization_code'
				,'code'      => $sCode
			)
			,90
			,'Basic '.base64_encode(ConfigService::getItem('docusign_integration_key').':'.ConfigService::getItem('docusign_secret_key'))
		);
		//log_message('required','Docusign getToken: '.print_r($aResponse,true));
		
		$aResponse = @json_decode($aResponse['content'],true);
		if (!empty($aResponse['access_token'])) {
			// Store info
			$aUserInfo = $this->getUserInfo('Bearer '.$aResponse['access_token']);

			Service::load('docusignaccesstoken')->addDocusignAccessToken(new DocusignAccessTokenModel(array(
				'parent_id'      => $this->_iParentId
				,'member_id'     => $this->_iMemberId
				,'access_token'  => $aResponse['access_token']
				,'token_type'    => $aResponse['token_type']
				,'refresh_token' => $aResponse['refresh_token']
				,'expires_in'    => $aResponse['expires_in']
				,'name'          => $aUserInfo['name']
				,'email'         => $aUserInfo['email']
				,'account_id'    => $aUserInfo['account_id']
				,'base_uri'      => $aUserInfo['base_uri']
				,'account_name'  => $aUserInfo['account_name']
			)));
			return true;
		}
		
		//log_message('required','Docusign refresh_token: '.print_r($aResponse,true));
		return false;
	}

	/*
		/oauth/userinfo
		Authorization: Bearer eyA3J3ad.k32jdskd.ann4ds	
		Once you have a token, use the userinfo endpoint to get the user’s account and base url informatioin. You’ll use these for subsequent API requests.
	*/
	protected function getUserInfo($sAuth) {
		$aResponse = $this->docusignapi->getUrlContents(
			"https://{$this->sDocuSignBase}/oauth/userinfo"
			,true
			,$sAuth
		);

		//log_message('required','Docusign refresh_token: '.print_r($aResponse,true));
		$aAccount = array(
			'account_id' => null
		);
		$aContent = json_decode($aResponse['content'],true);
		if (!empty($aContent['accounts'])) {
			$aAccount['name'] = $aContent['name'];
			$aAccount['email'] = $aContent['email'];

			foreach ($aContent['accounts'] as $aDocuSignAccount) {
				if (!empty($aDocuSignAccount['is_default'])) {
					$aAccount['account_id'] = $aDocuSignAccount['account_id'];
					$aAccount['base_uri'] = $aDocuSignAccount['base_uri'];
					$aAccount['account_name'] = $aDocuSignAccount['account_name'];
				}
			}
		}
		echo '<pre>'; var_dump($aContent,$aAccount);
		return $aAccount;
	}

	/*
		POST /oauth/token
		Host: account.docusign.com
		Content-Type: application/x-www-form-urlencoded
		Authorization: Basic MjMwNTQ2YTctOWM1NS00MGFkLThmYmYtYWYyMDVkNTQ5NGFkOjMwODc1NTVlLTBhMWMtNGFhOC1iMzI2LTY4MmM3YmYyNzZlOQ==
		
		grant_type=refresh_token&refresh_token=ey4fdd3nd.AAAA3d2ddagq3.akd1243d31d
	*/
	public function refresh_token() {
		$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array('parent_id' => $this->_iParentId))->reset();
		//echo '<pre>'; var_dump($oToken); return true;
		$this->load->library('DocuSignInterface');
		return $this->docusigninterface->refreshToken($oToken);
	}

	public function cron_refresh_tokens() {
		log_message('required',__METHOD__.' start');
		$this->load->library('DocuSignInterface');
		do {
			$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array(
				'last_updated <=' => date('Y-m-d H:i:s', strtotime('-24 hours'))
				,'status'       => 0
			))->reset();
			//echo '<pre>'; var_dump($oToken); return true;
			if (!empty($oToken)) {
				log_message('required',__METHOD__.' processing: '.$oToken->docusign_access_token_id);
				$bSuccess = $this->docusigninterface->refreshToken($oToken);

				if (!$bSuccess) {
					$this->_sendExpiredTokenEmail($oToken);
				}
			}
		} while (!empty($oToken));
		log_message('required',__METHOD__.' finish');
		
		ConfigService::loadFile('healthchecks');
		$hc_checks = ConfigService::getItem('hc_checks');
		file_get_contents($hc_checks['docusign_refresh_tokens']);
	}

	protected function _sendExpiredTokenEmail($oToken) {
		$sMessageHTML = $this->load->view('emails/docusign_token_expired',array(),true);
		$sMessageText = $this->lang->line('docusign_expired_token_notification');
		$sMessageSubject = $this->lang->line('docusign_expired_token_subject');

		$bSent = HelperService::sendEmail(
			$oToken->email,
			ConfigService::getItem('support_email'),
			$sMessageSubject,
			$sMessageText,
			$sMessageHTML
		);

		log_message('required','docusign_push_email email sent: '. ($bSent?1:0));
		return $bSent;
	}

	public function cron_send_contracts() {
	    require_once 'vendor/autoload.php';
	    $dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
	    $dotenv->load();
	    
	    putenv("AWS_SHARED_CREDENTIALS_FILE=" . $_ENV['AWS_SHARED_CREDENTIALS_FILE']);
	    
		$oDocusignContracts = Service::load('docusigncontract')->getDocusignContracts(array(
			'status' => DocusignContractModel::STATUS_PENDING
			,'(`last_checked` IS NULL OR `last_checked` <= \''.date('Y-m-d H:i:s',strtotime('-1 hour')).'\')'
		),'last_checked asc',50);
		
		log_message('required', 'aws_credential_path = ' . getenv('AWS_SHARED_CREDENTIALS_FILE'));
		log_message('required', 'SQL: ' . $this->db->last_query());
		
		foreach ($oDocusignContracts as $oDocusignContract) {
			if (Service::load('DocuSignAPI')->pushDocument($oDocusignContract)) {
				$oDocusignContract->status = DocusignContractModel::STATUS_SENT_TO_DOCUSIGN;

				$oContract = Service::load('contract')->getContracts(array(
					'contract_id' => $oDocusignContract->contract_id
				))->first();
				Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'member_id'    => 0
					,'contract_id' => $oDocusignContract->contract_id
					,'message'     => 'Document has been pushed to DocuSign'
					,'type'        => ContractLogModel::TYPE_DOCUSIGN_GENERIC
					,'create_date' => date('Y-m-d H:i:s')
				)));
				$this->_sendPushedEmail($oContract);
			}

			$oDocusignContract->last_checked = date('Y-m-d H:i:s');
			Service::load('docusigncontract')->updateDocusignContract($oDocusignContract);
		}
		
		ConfigService::loadFile('healthchecks');
		$hc_checks = ConfigService::getItem('hc_checks');
		file_get_contents($hc_checks['docusign_send_contracts']);
	}

	public function test_send_contract() {
		$oDocusignContracts = Service::load('docusigncontract')->getDocusignContracts(array(
			'contract_id' => 1160
		),'last_checked asc',50);
		
		foreach ($oDocusignContracts as $oDocusignContract) {
			if (Service::load('DocuSignAPI')->pushDocument($oDocusignContract)) {
				$oDocusignContract->status = DocusignContractModel::STATUS_SENT_TO_DOCUSIGN;

				$oContract = Service::load('contract')->getContracts(array(
					'contract_id' => $oDocusignContract->contract_id
				))->first();
				Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'member_id'    => 0
					,'contract_id' => $oDocusignContract->contract_id
					,'message'     => 'Document has been pushed to DocuSign'
					,'type'        => ContractLogModel::TYPE_DOCUSIGN_GENERIC
					,'create_date' => date('Y-m-d H:i:s')
				)));
				$this->_sendPushedEmail($oContract);
			}

			$oDocusignContract->last_checked = date('Y-m-d H:i:s');
			Service::load('docusigncontract')->updateDocusignContract($oDocusignContract);
		}
	}

	protected function _sendPushedEmail($oContract) {
		$sSubject = $this->lang->line('docusign_push_email_subject');

		$sMessageText = $this->lang->line('docusign_push_email');
		$sMessageText = str_replace('%%CONTRACT_NAME%%',$oContract->name,$sMessageText);
		$sMessageHTML = $this->load->view('emails/docusign_push_contract',array(
			'sContractName' => $oContract->name
		),true);

		$oUploader = Service::load('member')->getMembers(array('member_id'=>$oContract->parent_id))->reset();

		$bSent = HelperService::sendEmail(
			$oUploader->email,
			ConfigService::getItem('support_email'),
			$sSubject,
			$sMessageText,
			$sMessageHTML
		);

		log_message('required','docusign_push_email email sent: '. ($bSent?1:0));
		return $bSent;
	}

	public function cron_check_contracts() {
		$oDocusignContracts = Service::load('docusigncontract')->getDocusignContracts(array(
			'status' => array(
				DocusignContractModel::STATUS_SENT_TO_DOCUSIGN
				,DocusignContractModel::STATUS_SENT_TO_SIGNERS
			)
			,'(`last_checked` IS NULL OR `last_checked` <= \''.date('Y-m-d H:i:s',strtotime('-1 hour')).'\')'
		),'last_checked asc',50);
		
		//echo '<pre>'; 
		foreach ($oDocusignContracts as $oDocusignContract) {
		    $oContract = Service::load('contract')->getContract(array('contract_id'=>$oDocusignContract->contract_id, 'docusign_error' => 0))->first();
		    
			if (empty($oContract)) {
				$oDocusignContract->status = DocusignContractModel::STATUS_CHC_MISSING;
				$oDocusignContract->last_checked = date('Y-m-d H:i:s');
				Service::load('docusigncontract')->updateDocusignContract($oDocusignContract);
				continue;
			}

			$sMessage = '';
			$iPreviousState = $oDocusignContract->status;
			$mResult = Service::load('DocuSignAPI')->getDocumentStatus($oContract->parent_id,$oDocusignContract);
			switch ($mResult) {
				case 'sent':
				case 'delivered':
					$sMessage = 'DocuSign as notified signers to sign.';
					$oDocusignContract->status = DocusignContractModel::STATUS_SENT_TO_SIGNERS;
					$this->_updateSigners($oDocusignContract);
					break;
				case 'signed':
				case 'completed':
					Service::load('DocuSignAPI')->downloadDocument($oDocusignContract);
					$this->_updateSigners($oDocusignContract);
					$this->_sendPullEmail($oDocusignContract);
					$oDocusignContract->status = DocusignContractModel::STATUS_COMPLETED;
					$sMessage = 'Document is fully executed and synced back to ContractHound.';
					break;
				case 'rejected':
				case 'declined':
					$this->_updateSigners($oDocusignContract);
					$this->_sendRejectEmail($oDocusignContract);
					$oDocusignContract->status = DocusignContractModel::STATUS_REJECTED;
					$sMessage = 'Document was rejected by a signer.';
					break;
				case 'timedout':
				case 'voided':
				case 'deleted':
					$sMessage = 'Document signing was cancelled.';
					$oDocusignContract->status = DocusignContractModel::STATUS_CANCELED;
					break;	
			}
			
			if ($iPreviousState != $oDocusignContract->status) {
				Service::load('contractlog')->addContractLog(new ContractLogModel(array(
					'member_id'    => 0
					,'contract_id' => $oContract->contract_id
					,'message'     => $sMessage
					,'type'        => ContractLogModel::TYPE_DOCUSIGN_GENERIC
					,'create_date' => date('Y-m-d H:i:s')
				)));
			}

			//var_dump($oDocusignContract->docusign_contract_id.' '.$mResult);
			$oDocusignContract->last_checked = date('Y-m-d H:i:s');
			Service::load('docusigncontract')->updateDocusignContract($oDocusignContract);
		}
		
		ConfigService::loadFile('healthchecks');
		$hc_checks = ConfigService::getItem('hc_checks');
		file_get_contents($hc_checks['docusign_check_contracts']);
	}

	public function test_signer_status_update() {
		$oDocusignContract = Service::load('docusigncontract')->getDocusignContracts(array(
			'docusign_contract_id' => 2
		))->first();
		
		$this->_updateSigners($oDocusignContract);
	}

	protected function _updateSigners($oDocusignContract) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $oDocusignContract->contract_id
		))->first();
		if (empty($oContract)) {
			return false;
		}

		$oRecipients = Service::load('DocuSignAPI')->getSignerStatus($oContract->parent_id,$oDocusignContract);
		//echo '<pre>';
		$oSignatures = Service::load('contractsignature')->getContractSignatures(array(
			'contract_id' => $oContract->contract_id
		));

		$aSignatureMembersToGet = array();
		$aSortedSignatures = array();
		foreach ($oSignatures as $oSignature) {
			$aSortedSignatures[$oSignature->member_id] = $oSignature;
			$aSignatureMembersToGet[$oSignature->member_id] = $oSignature->member_id;
		}
		unset($oSignatures);

		if (!empty($aSignatureMembersToGet)) {
			$oSignatureMembersToSort = Service::load('member')->getMembers(array(
				'parent_id'  => $oContract->parent_id
				,'member_id' => $aSignatureMembersToGet
			));

			foreach ($oSignatureMembersToSort as $oSignatureMemberToSort) {
				$aSignatureMembers[strtolower($oSignatureMemberToSort->email)] = $oSignatureMemberToSort;
			}
		}
		
		foreach ($oRecipients->getSigners() as $oRecipient) {
			$sRecipientEmail = strtolower(trim($oRecipient->getEmail()));
			//var_dump($oRecipient);

			if (isset($aSignatureMembers[$sRecipientEmail])) {
				$oMember = $aSignatureMembers[$sRecipientEmail];
				$oContractSignature = $aSortedSignatures[$oMember->member_id];
				$iPreviousStatus = $oContractSignature->status;

				$bSigned = false;
				switch ($oRecipient->getStatus()) {
					case 'created':
						break;
					case 'declined':
						$oContractSignature->status = ContractSignatureModel::STATUS_REJECTED;
						break;
					case 'signed': // ????
					case 'completed':
						$bSigned = true;
						$oContractSignature->status = ContractSignatureModel::STATUS_SIGNED;
						break;
				}

				//var_dump($oContractSignature);
				if ($iPreviousStatus != $oContractSignature->status) {
					Service::load('contractlog')->addContractLog(new ContractLogModel(array(
						'member_id'    => 0
						,'contract_id' => $oContractSignature->contract_id
						,'message'     => $bSigned ? 'Signed contract' : 'Rejected contract'
						,'type'        => $bSigned ? ContractLogModel::TYPE_SIGNER_APPROVED : ContractLogModel::TYPE_SIGNER_REJECTED
						,'create_date' => date('Y-m-d H:i:s')
					)));
				}
				Service::load('contractsignature')->updateContractSignature($oContractSignature);
			}
		}
	}

	public function test_pulled_email() {
		$oDocusignContract = Service::load('docusigncontract')->getDocusignContracts(array(
			'docusign_contract_id' => 2
		))->first();
		$this->_sendPullEmail($oDocusignContract);
	}

	protected function _sendRejectEmail($oDocusignContract) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $oDocusignContract->contract_id
		))->first();
		
		$oSignatures = Service::load('contractsignature')->getContractSignatures(array(
			'contract_id' => $oDocusignContract->contract_id
			,'status'     => ContractSignatureModel::STATUS_REJECTED
		));

		$aSignatureMembers = array();
		$aSignatureMembersToGet = array();
		foreach ($oSignatures as $oSignature) {
			$aSignatureMembersToGet[$oSignature->member_id] = $oSignature->member_id;
		}
		$oSignatureMembersToSort = Service::load('member')->getMembers(array('member_id'=>$aSignatureMembersToGet));
		foreach ($oSignatureMembersToSort as $oSignatureMemberToSort) {
			$aSignatureMembers[] = $oSignatureMemberToSort->name;
		}

		$sSubject = $this->lang->line('docusign_rejected_email_subject');

		$sMessageText = $this->lang->line('docusign_rejected_email');
		$sMessageText = str_replace('%%CONTRACT_NAME%%',$oContract->name,$sMessageText);
		$sMessageText = str_replace('%%SIGNERS%%',implode(', ',$aSignatureMembers),$sMessageText);
		$sMessageText = str_replace('%%VERB%%',((count($aSignatureMembers) > 1) ? 'have' : 'has'),$sMessageText);
		$sMessageHTML = $this->load->view('emails/docusign_reject_contract',array(
			'sContractName' => $oContract->name
			,'sSigners' => implode(', ',$aSignatureMembers)
			,'sVerb'    =>  (count($aSignatureMembers) > 1) ? 'have' : 'has'
		),true);

		$oUploader = Service::load('member')->getMembers(array('member_id'=>$oContract->owner_id))->reset();

		$bSent = HelperService::sendEmail(
			$oUploader->email,
			ConfigService::getItem('support_email'),
			$sSubject,
			$sMessageText,
			$sMessageHTML
		);

		log_message('required','docusign_push_email email sent: '. ($bSent?1:0));
		return $bSent;
	}

	protected function _sendPullEmail($oDocusignContract) {
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $oDocusignContract->contract_id
		))->first();
		
		$oSignatures = Service::load('contractsignature')->getContractSignatures(array(
			'contract_id' => $oDocusignContract->contract_id
		));

		$aSignatureMembers = array();
		$aSignatureMembersToGet = array();
		foreach ($oSignatures as $oSignature) {
			$aSignatureMembersToGet[$oSignature->member_id] = $oSignature->member_id;
		}
		$oSignatureMembersToSort = Service::load('member')->getMembers(array('member_id'=>$aSignatureMembersToGet));
		foreach ($oSignatureMembersToSort as $oSignatureMemberToSort) {
			$aSignatureMembers[] = $oSignatureMemberToSort->name;
		}

		$sSubject = $this->lang->line('docusign_pull_email_subject');

		$sMessageText = $this->lang->line('docusign_pull_email');
		$sMessageText = str_replace('%%CONTRACT_NAME%%',$oContract->name,$sMessageText);
		$sMessageText = str_replace('%%SIGNERS%%',implode(', ',$aSignatureMembers),$sMessageText);
		$sMessageText = str_replace('%%VERB%%',((count($aSignatureMembers) > 1) ? 'have' : 'has'),$sMessageText);
		$sMessageHTML = $this->load->view('emails/docusign_pull_contract',array(
			'sContractName' => $oContract->name
			,'sSigners' => implode(', ',$aSignatureMembers)
			,'sVerb'    =>  (count($aSignatureMembers) > 1) ? 'have' : 'has'
		),true);

		$oUploader = Service::load('member')->getMembers(array('member_id'=>$oContract->owner_id))->reset();

		$bSent = HelperService::sendEmail(
			$oUploader->email,
			ConfigService::getItem('support_email'),
			$sSubject,
			$sMessageText,
			$sMessageHTML
		);

		log_message('required','docusign_push_email email sent: '. ($bSent?1:0));
		return $bSent;
	}

	public function test_signer_status() {
		$oDocusignContract = Service::load('docusigncontract')->getDocusignContracts(array(
			'docusign_contract_id' => 2
		))->first();

		$oRecipients = Service::load('DocuSignAPI')->getSignerStatus(1,$oDocusignContract);
		
		echo '<pre>';
		//var_dump($oRecipients);
		foreach ($oRecipients->getSigners() as $oSigner) {
			var_dump($oSigner->getEmail(),$oSigner->getStatus());
		}
	}

	//protected function _sendCompletedEmail($this->)

	public function get_doc() {
		$oDocusignContract = Service::load('docusigncontract')->getDocusignContracts(array(
			'docusign_contract_id' => 2
		))->first();
		
		$mResult = Service::load('DocuSignAPI')->downloadDocument($oDocusignContract);
		echo '<pre>';
		var_dump($mResult);
	}
}