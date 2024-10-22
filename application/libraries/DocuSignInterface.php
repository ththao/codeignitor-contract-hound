<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * DocuSignInterface
 *
 * @access public
 */
require_once('DocusignAPI.php');
require_once(APPPATH.'services/DocusignAccessTokenService.php');

class DocuSignInterface {
	//protected $sDocuSignBase = 'account-d.docusign.com'; // for the developer sandbox
	protected $sDocuSignBase = 'account.docusign.com'; // for the production platform.

	protected $_oDocuSignAPI = null;

	public function __construct() {
		$this->_oDocuSignAPI = new DocusignAPI();
	}
	
	//https://docs.docusign.com/esign/guide/authentication/oa2_auth_code.html
	public function refreshToken(DocusignAccessTokenModel $oToken) {
		$aResponse = $this->_oDocuSignAPI->postUrlContents(
			"https://{$this->sDocuSignBase}/oauth/token"
			,array(
				'grant_type'     => 'refresh_token'
				,'refresh_token' => $oToken->refresh_token
			)
			,90
			,'Basic '.base64_encode(ConfigService::getItem('docusign_integration_key').':'.ConfigService::getItem('docusign_secret_key'))
		);

		log_message('required','Docusign updating token: '.$oToken->docusign_access_token_id);
		log_message('required','Docusign refresh_token: '.print_r($aResponse,true));

		$aToken = @json_decode($aResponse['content'],true);
		if (!empty($aToken['access_token'])) {
			//log_message('required','updated token: '.$oToken->docusign_access_token_id);
			//echo '<pre>'; var_dump($aResponse); return true;

			return $this->updateToken($oToken,$aToken);
		} else {
			$oToken->status = DocusignAccessTokenModel::STATUS_EXPIRED;
			Service::load('docusignaccesstoken')->updateDocusignAccessToken($oToken);
			//log_message('error','unable to update token: '.$oToken->docusign_access_token_id.' '.print_r($aResponse,true));

			return false;
		}
		
		return false;
	}

	public function updateToken($oToken,$aToken) {
		$oToken->access_token  = $aToken['access_token'];
		$oToken->token_type    = $aToken['token_type'];
		$oToken->refresh_token = $aToken['refresh_token'];
		$oToken->expires_in    = $aToken['expires_in'];
		return Service::load('docusignaccesstoken')->updateDocusignAccessToken($oToken);
	}
}
