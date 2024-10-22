<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DocuSign Service Class
 *
 * @access public
 */
class DocuSignAPIService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	protected $_aValidFileExtensions = array(
		'.as'
		,'.asl'
		,'.asp'
		,'.doc'
		,'.docm'
		,'.docx'
		,'.dot'
		,'.dotm'
		,'.dotx'
		,'.htm'
		,'.html'
		,'.pdf'
		,'.pdx'
		,'.rtf'
		,'.txt'
		,'.wpd'
		,'.wps'
		,'.wpt'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  Class Methods   ///////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
	}

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	protected function _getUpdatedToken($iParentId) {
		$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array(
			'parent_id' => $iParentId
			,'status'   => DocusignAccessTokenModel::STATUS_ACTIVE
		))->reset();
		
		if (empty($oToken)) {
			return false;
		}
		
		if ($oToken->expires_at <= date('Y-m-d H:i:s')) {
			$this->_getLibrary('DocuSignInterface');
			try {
				$oToken = $this->_getLibrary('DocuSignInterface')->refreshToken($oToken);
				if (empty($oToken)) {
					return false;
				}
			} catch (Exception $e) {
				log_message('error',__METHOD__.' exception: '.$e->getMessage());
				return false;
			}
		}
		
		return $oToken;
	}

	public function downloadDocument($oDocusignContract) {
		$oContract = Service::load('contract')->getContract(array(
			'contract_id' => $oDocusignContract->contract_id
		))->first();

		if (empty($oContract)) {
			return $this->_setupErrorResponse(ServiceResponse::StatusBadRequest,'Contract not found.');
		}

		$oToken = $this->_getUpdatedToken($oContract->parent_id);
		if (empty($oToken)) {
			return false;
		}

		try {
			$oConfig = new DocuSign\eSign\Configuration();
			$oConfig->setHost($oToken->base_uri.'/restapi');
			$oConfig->addDefaultHeader('Authorization',"Bearer {$oToken->access_token}");
			
			$oAPIClient = new DocuSign\eSign\ApiClient($oConfig);
	
			$oEnvelopeApi = new DocuSign\eSign\Api\EnvelopesApi($oAPIClient);
	
			$oDocsList = $oEnvelopeApi->listDocuments($oToken->account_id, $oDocusignContract->docusign_envelope_id);
	
			$iDocCount = count($oDocsList->getEnvelopeDocuments());
			if (intval($iDocCount) > 0)	{
				foreach($oDocsList->getEnvelopeDocuments() as $oDocument) {
					if (!is_numeric($oDocument->getDocumentId())) {
						continue;
					}
	
					$oFile = $oEnvelopeApi->getDocument($oToken->account_id, $oDocument->getDocumentId(), $oDocusignContract->docusign_envelope_id);
					$oContract->file_name = str_replace('/tmp/','',$oFile->getPathName()).'.pdf';
					$oUpdate = Service::load('contract')->updateContract($oContract, $oFile->getPathName());
		
					if ($oUpdate->isOk()) {
						$oContract = $oUpdate->reset();
						$oAddContractLog = Service::load('contractlog')->addContractLog(new ContractLogModel(array(
							'member_id'    => 0
							,'contract_id' => $oContract->contract_id
							,'message'     => 'Downloaded from DocuSign'
							,'type'        => ContractLogModel::TYPE_UPDATE
							,'create_date' => date('Y-m-d H:i:s')
						)));
		
						Service::load('contractrevision')->generateRevision($oContract);

						//$oDocusignContract->status = DocusignContractModel::STATUS_COMPLETED;
						//Service::load('DocusignContract')->updateDocusignContract($oDocusignContract);

						return true;
					}
				}
			}
		} catch (Exception $e) {
			log_message('error',__METHOD__.' failed: '.$e->getMessage());
		}
		
		return false;
	}

	public function getDocumentStatus($iParentId,$oDocusignContract) {
		$oToken = $this->_getUpdatedToken($iParentId);
		if (empty($oToken)) {
			return $this->_setupErrorResponse(ServiceResponse::StatusBadRequest,'Unable to find valid DocuSign Token');
		}
		
		$oConfig = new DocuSign\eSign\Configuration();
		$oConfig->setHost($oToken->base_uri.'/restapi');
		$oConfig->addDefaultHeader('Authorization',"Bearer {$oToken->access_token}");
		
		$oAPIClient = new DocuSign\eSign\ApiClient($oConfig);

		$oEnvelopeApi = new DocuSign\eSign\Api\EnvelopesApi($oAPIClient);

		$oOptions = new \DocuSign\eSign\Api\EnvelopesApi\GetEnvelopeOptions();
		$oOptions->setInclude(null);

		$oLROptions = new \DocuSign\eSign\Api\EnvelopesApi\ListRecipientsOptions();

		try {
		    $oEnvelope = $oEnvelopeApi->getEnvelope($oToken->account_id, $oDocusignContract->docusign_envelope_id, $oOptions);
		    log_message('required',print_r($oEnvelope,true));
		    if (!empty($oEnvelope)) {
			    $oSigs = $oEnvelopeApi->listRecipients($oToken->account_id, $oDocusignContract->docusign_envelope_id, $oLROptions);
				log_message('required',print_r($oSigs,true));
			    return $oEnvelope->getStatus();
		    }
		} catch (Exception $e) {
		    log_message('error',__METHOD__.' '.$e->getMessage());
		    
		    $oContract = Service::load('contract')->getContract(array(
		        'contract_id' => $oDocusignContract->contract_id,
		        'docusign_error' => 0
		    ))->first();
		    
		    Service::load('contractlog')->addContractLog(new ContractLogModel(array(
		        'member_id'    => $oContract->parent_id
		        ,'contract_id' => $oDocusignContract->contract_id
		        ,'message'     => 'There was an error when trying to get this document status from DocuSign. Please contact support!'
		        ,'type'        => ContractLogModel::TYPE_DOCUSIGN_GENERIC
		        ,'create_date' => date('Y-m-d H:i:s')
		    )));
		    
		    $oContract->docusign_error = ContractModel::DOCUSIGN_ERROR_GET;
		    Service::load('contract')->updateContract($oContract);
		    
		    $bugsnag = Bugsnag\Client::make($_ENV['BUGSNAG_API_KEY']);
		    $bugsnag->setReleaseStage(ENVIRONMENT);
		    $bugsnag->notifyException($e);
		}

		return false;
	}

	public function getSignerStatus($iParentId,$oDocusignContract) {
		$oToken = $this->_getUpdatedToken($iParentId);
		if (empty($oToken)) {
			return $this->_setupErrorResponse(ServiceResponse::StatusBadRequest,'Unable to find valid DocuSign Token');
		}

		$oConfig = new DocuSign\eSign\Configuration();
		$oConfig->setHost($oToken->base_uri.'/restapi');
		$oConfig->addDefaultHeader('Authorization',"Bearer {$oToken->access_token}");
		
		$oAPIClient = new DocuSign\eSign\ApiClient($oConfig);

		$oEnvelopeApi = new DocuSign\eSign\Api\EnvelopesApi($oAPIClient);

		$oLROptions = new \DocuSign\eSign\Api\EnvelopesApi\ListRecipientsOptions();

		try {
		    $oSigs = $oEnvelopeApi->listRecipients($oToken->account_id, $oDocusignContract->docusign_envelope_id, $oLROptions);
		    return $oSigs;
			log_message('required',print_r($oSigs,true));
		} catch (Exception $e) {
			log_message('error',__METHOD__.' '.$e->getMessage());
		}

		return false;
	}


	public function pushDocument($oDocusignContract) {
		$oContract = Service::load('contract')->getContract(array(
			'contract_id' => $oDocusignContract->contract_id,
		    'docusign_error' => 0
		))->first();
		
		if (empty($oContract)) {
			//return $this->_setupErrorResponse(ServiceResponse::StatusBadRequest,'Contract not found.');
			return false;
		}

		$oToken = $this->_getUpdatedToken($oContract->parent_id);
		if (empty($oToken)) {
			//return $this->_setupErrorResponse(ServiceResponse::StatusBadRequest,'Unable to find valid DocuSign Token');
		    Service::load('contractlog')->addContractLog(new ContractLogModel(array(
		        'member_id'    => $oContract->parent_id
		        ,'contract_id' => $oDocusignContract->contract_id
		        ,'message'     => 'Unable to find valid DocuSign Token'
		        ,'type'        => ContractLogModel::TYPE_DOCUSIGN_GENERIC
		        ,'create_date' => date('Y-m-d H:i:s')
		    )));
		    
		    $oContract->docusign_error = 1;
		    Service::load('contract')->updateContract($oContract);
			return false;
		}

		$oContractSigners = Service::load('contractsignature')->getContractSignatures(array(
			'contract_id' => $oDocusignContract->contract_id
		));
		if ($oContractSigners->count == 0) {
			//return $this->_setupErrorResponse(ServiceResponse::StatusBadRequest,'Signers not found.');
			Service::load('contractlog')->addContractLog(new ContractLogModel(array(
			    'member_id'    => $oContract->parent_id
			    ,'contract_id' => $oDocusignContract->contract_id
			    ,'message'     => 'Signers not found'
			    ,'type'        => ContractLogModel::TYPE_DOCUSIGN_GENERIC
			    ,'create_date' => date('Y-m-d H:i:s')
			)));
			
			$oContract->docusign_error = 1;
			Service::load('contract')->updateContract($oContract);
			return false;
		}
		
		$aFilenameParts = explode('.',$oContract->file_name);
		$sExt = end($aFilenameParts);
		if (!in_array('.'.$sExt,$this->_aValidFileExtensions)) {
			//return $this->_setupErrorResponse(ServiceResponse::StatusBadRequest,'Filetype not supported by DocuSign. '.$sExt);
			return false;
		}

		/* old way
		$aOptions = array(
			'adapter'   => 'BlockCipher',
			'vector'    => md5($oContract->file_hash.md5('h53bhq35hq34&^(&R%4hhb'.$oContract->contract_id)),
			'algorithm' => 'rijndael-192',
			'key'       => md5('superRoper%&#@$72345'.$oContract->file_hash.md5($oContract->contract_id))
		);
		$sFilename = $oContract->file_hash;
		$sFullPath = '/var/www/html/ctcssa/'.substr($sFilename,0,1).'/'.substr($sFilename,1,1).'/'.substr($sFilename,2,1).'/'.$sFilename;
		$oEncrypt = new Zend\Filter\File\Decrypt($aOptions);
		$sTmpPath = '/tmp/'.$oContract->owner_id.'_DSP_'.$oContract->file_hash.'.'.$sExt;
		touch($sTmpPath);
		@chmod($sTmpPath,0777);
		$oEncrypt->setFilename($sTmpPath);
		$oEncrypt->filter($sFullPath);
		*/
		$mResponse = Service::load('contract')->retrieveFile($oContract);
		if (empty($mResponse)) {
			log_message('error',__METHOD__.' '.$oContract->contract_id.' not retrieved');
			return false;
		}

		$sTmpPath = $mResponse.'.'.$sExt;
		@rename($mResponse,$sTmpPath);
		if (!file_exists($sTmpPath)) {
			log_message('error',__METHOD__.' '.$oContract->contract_id.' not renamed: '.$sTmpPath);
			return false;
		}
		
		$oConfig = new DocuSign\eSign\Configuration();
		$oConfig->setHost($oToken->base_uri.'/restapi');
		$oConfig->addDefaultHeader('Authorization',"Bearer {$oToken->access_token}");
		
		$oAPIClient = new DocuSign\eSign\ApiClient($oConfig);

		$oEnvelopeApi = new DocuSign\eSign\Api\EnvelopesApi($oAPIClient);

		// Add a document to the envelope
		$oDocument = new DocuSign\eSign\Model\Document();
		$sFileContent = file_get_contents($sTmpPath);
		$oDocument->setDocumentBase64(base64_encode($sFileContent));
		$oDocument->setName($oContract->file_name);
		$oDocument->setFileExtension($sExt);
		$oDocument->setDocumentId('1');

		$oSigner = new \DocuSign\eSign\Model\Signer();
		foreach ($oContractSigners as $i => $oContractSigner) {
			$iTempSignerId = $i + 1;
			$oMember = Service::load('member')->getMember(array(
				'member_id' => $oContractSigner->member_id
			))->first();

			$oSigner->setEmail($oMember->email);
			$oSigner->setName($oMember->name?$oMember->name:$oMember->email);
			$oSigner->setRecipientId($iTempSignerId);
		}

		$oRecipients = new DocuSign\eSign\Model\Recipients();
		$oRecipients->setSigners(array($oSigner));

		$oEnvelopDefinition = new DocuSign\eSign\Model\EnvelopeDefinition();
		$oEnvelopDefinition->setEmailSubject(date('Y-m-d H:i:s')." - Please sign this doc");

		$oEnvelopDefinition->setStatus('created');
		$oEnvelopDefinition->setRecipients($oRecipients);
		$oEnvelopDefinition->setDocuments(array($oDocument));

		$oOptions = new \DocuSign\eSign\Api\EnvelopesApi\CreateEnvelopeOptions();
		$oOptions->setCdseMode(null);
		$oOptions->setMergeRolesOnDraft(null);

		try {
			$oEnvelopSummary = $oEnvelopeApi->createEnvelope($oToken->account_id, $oEnvelopDefinition, $oOptions);
			log_message('required','oEnvelopSummary: '.print_r($oEnvelopSummary,true));
			
			// update somewhere?
			$sEnvelopeId = $oEnvelopSummary->getEnvelopeId();
			@unlink($sTmpPath);

			if (!empty($sEnvelopeId)) {
				$oDocusignContract->docusign_envelope_id = $sEnvelopeId;
				$oDocusignContract->status = DocusignContractModel::STATUS_SENT_TO_DOCUSIGN;
				$oDocusignContract->last_checked = date('Y-m-d H:i:s');
				Service::load('docusigncontract')->updateDocusignContract($oDocusignContract);
				return true;
			}
		} catch (Exception $e) {
			@unlink($sTmpPath);
			
			Service::load('contractlog')->addContractLog(new ContractLogModel(array(
			    'member_id'    => $oContract->parent_id
			    ,'contract_id' => $oDocusignContract->contract_id
			    ,'message'     => 'There was an error sending this document to DocuSign. Please contact support!'
			    ,'type'        => ContractLogModel::TYPE_DOCUSIGN_GENERIC
			    ,'create_date' => date('Y-m-d H:i:s')
			)));
			
			$oContract->docusign_error = ContractModel::DOCUSIGN_ERROR_CREATE;
			Service::load('contract')->updateContract($oContract);
			
			$bugsnag = Bugsnag\Client::make($_ENV['BUGSNAG_API_KEY']);
			$bugsnag->setReleaseStage(ENVIRONMENT);
			$bugsnag->notifyException($e);
			
			log_message('error',__METHOD__.' caught exception: '.$e->getMessage());
		}

		return false;
	}
}
