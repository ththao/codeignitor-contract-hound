<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Billing Info Service Class
 *
 * @access public
 */
class DocusignAccessTokenService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'DocusignAccessTokenModel';

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

	public function calculateExpiresAt($oDocusignAccessToken) {
		$oDocusignAccessToken->expires_at = date('Y-m-d H:i:s',strtotime('+'.$oDocusignAccessToken->expires_in.' seconds',strtotime($oDocusignAccessToken->last_updated)));
		return $oDocusignAccessToken;
	}

	/**
	 * Add Billing Info
	 *
	 * @access public
	 * @param DocusignAccessTokenModel $oDocusignAccessToken
	 * @return ServiceResponse
	 */
	public function addDocusignAccessToken(DocusignAccessTokenModel $oDocusignAccessToken) {
		$oDocusignAccessToken->create_date = date('Y-m-d H:i:s');
		$oDocusignAccessToken->last_updated = date('Y-m-d H:i:s');
		$oDocusignAccessToken = $this->calculateExpiresAt($oDocusignAccessToken);

		$iResult = $this->_getModel('docusign_access_token_m')->addItem($oDocusignAccessToken->toArray());

		if ($iResult) {
			$oDocusignAccessToken->docusign_access_token_id = $iResult;
			$oDocusignAccessToken->isSaved(true);
			return new ServiceResponse(array($oDocusignAccessToken));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Billing Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteDocusignAccessToken($aFilters) {
		$bResponse = $this->_getModel('docusign_access_token_m')->deleteItems($aFilters);

		if ($bResponse) {
			return new ServiceResponse();
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Billing Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getDocusignAccessToken($aFilters=array()) {
		$aDocusignAccessToken = $this->_getModel('docusign_access_token_m')->getItem($aFilters);

		if (!empty($aDocusignAccessToken)) {
			return $this->_setupResponse(array($aDocusignAccessToken));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Billing Info
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getDocusignAccessTokens($aFilters=array()) {
		$aDocusignAccessTokens = $this->_getModel('docusign_access_token_m')->getItems($aFilters);
		return $this->_setupResponse($aDocusignAccessTokens);
	}

	/**
	 * Get Last Billing Info
	 *
	 * @access public
	 * @param integer $iMemberId
	 * @return ServiceResponse
	 */
	public function getLastDocusignAccessToken($iMemberId) {
		if (empty($iMemberId)) {
			return new DocusignAccessTokenModel();
		}

		$aDocusignAccessToken = $this->_getModel('docusign_access_token_m')->getItems(array(
			'member_id' => $iMemberId
		),'create_date desc',1);

		if (!empty($aDocusignAccessToken)) {
			return $this->_setupResponse($aDocusignAccessToken);
		}

		return new ServiceResponse(array(new DocusignAccessTokenModel()));
	}

	/**
	 * Update Billing Info
	 *
	 * @access public
	 * @param DocusignAccessTokenModel $oDocusignAccessToken
	 * @return ServiceResponse
	 */
	public function updateDocusignAccessToken(DocusignAccessTokenModel $oDocusignAccessToken) {
		$oDocusignAccessToken->last_updated = date('Y-m-d H:i:s');
		$oDocusignAccessToken = $this->calculateExpiresAt($oDocusignAccessToken);

		$bResponse = $this->_getModel('docusign_access_token_m')->updateItem($oDocusignAccessToken->toArray());
		if ($bResponse) {
			$oDocusignAccessToken->isSaved(true);
			return new ServiceResponse(array($oDocusignAccessToken));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
