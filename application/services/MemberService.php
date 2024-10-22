<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MemberService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'MemberModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function getApiKey($iMemberId) {
		$oMember = $this->getMember(array('member_id'=>$iMemberId))->first();

		if (empty($oMember)) {
			throw new Exception('Missing data for Api Key generation');
		}

		return md5('K"X%wBdn*S#'.$oMember->member_id.':8wR,WbSw'.$oMember->email.'YIb*@\'"x.0QU%'.$oMember->status.'qZPtJF"xvr@Zs&Z');
	}

	public function addFailedLoginAttempt($iMemberId) {
		if (empty($iMemberId)) {
			return false;
		}

		return $this->_getModel('members_m')->addFailedLoginAttempt($iMemberId);
	}

	public function resetFailedLoginAttempt($iMemberId) {
		return $this->_getModel('members_m')->resetFailedLoginAttempt($iMemberId);
	}

	/**
	 * Add Member
	 *
	 * @access public
	 * @param MemberModel $oMember
	 * @return ServiceResponse
	 */
	public function addMember(MemberModel $oMember) {
		$oMember->password = $this->encryptPassword($oMember->email,$oMember->password);

		$iResult = $this->_getModel('members_m')->addItem($oMember->toArray());
		if ($iResult) {
			$oMember->member_id = $iResult;
			$oMember->isSaved(true);
			return new ServiceResponse(array($oMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Confirm Member Password
	 *
	 * @access public
	 * @param MemberModel $oMember
	 * @param string $sPassword
	 * @return boolean
	 */
	public function confirmPassword(MemberModel $oMember,$sPassword) {
		$sEncodedPassword = $this->encryptPassword($oMember->email,strtolower($sPassword));

		if (strcmp($sEncodedPassword,$oMember->password) === 0) {
			return true;
		}

		$sEncodedPassword = $this->encryptPassword($oMember->email,$sPassword);

		if (strcmp($sEncodedPassword,$oMember->password) === 0) {
			return true;
		}

		return false;
	}

	/**
	 * Delete Members
	 *
	 * @access public
	 * @param MemberModel $oMember
	 * @return ServiceResponse
	 */
	public function deleteMember(MemberModel $oMember) {
		Service::load('alarm')->deleteAlarms(array('member_id'=>$oMember->member_id));
		//Service::load('connection')->deleteConnections(array('member_id'=>$oMember->member_id));
		Service::load('email')->deleteEmails(array('member_id'=>$oMember->member_id));
		Service::load('site')->deleteSites(array('member_id'=>$oMember->member_id));

		$oMember->status = MemberModel::StatusDeleted;
		return $this->updateMember($oMember);
	}

	/**
	 * Get Member
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getMember($aFilters=array()) {
		if (!empty($aFilters['email']) && !empty($aFilters['password'])) {
			$aFilters['password'] = $this->encryptPassword($aFilters['email'],$aFilters['password']);
		}

		$aMember = $this->_getModel('members_m')->getItem($aFilters);
		if (!empty($aMember)) {
			return $this->_setupResponse(array($aMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Returns the parent member model
	 *
	 * @param null $oMember
	 * @return null|ServiceResponse
	 */
	public function getParentMember($oMember = null) {
		if($oMember->member_id == $oMember->parent_id)
			return null;

		return $this->getMember(['member_id' => $oMember->parent_id]);
	}

	/**
	 * Get Member Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getMemberCount($aFilters=array()) {
		$iCount = $this->_getModel('members_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get Members
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getMembers($aFilters=array(),$sSort='member_id asc',$iLimit=null,$iOffset=null) {
		$aMembers = $this->_getModel('members_m')->getItems($aFilters,$sSort,$iLimit,$iOffset);
		return $this->_setupResponse($aMembers);
	}

	public function isAdmin($iMemberId) {
		$aAdmins = get_instance()->config->item('admin_ids');

		if (in_array($iMemberId,$aAdmins)) {
			return true;
		}

		return false;
	}

	public function sendConfirmationEmail(MemberModel $oMember) {
		log_message('required','confirmation: '.$oMember->email.' '.$oMember->getEmailConfirmationToken());

		$sToken = $oMember->getEmailConfirmationToken();
		$sUrl = site_url('members/confirm/'.$oMember->member_id).'?cfmtk='.$sToken;

		$sSubject = lang('member_confirmation_email_subject_new');
		$sMessageHTML = lang('member_confirmation_email_message_html_new');
		$sMessageHTML = str_replace(array('%%TOKEN%%','%%URL%%'), array($sToken,$sUrl), $sMessageHTML);

		$sMessageText = lang('member_confirmation_email_message_text_new');
		$sMessageText = str_replace(array('%%TOKEN%%','%%URL%%'), array($sToken,$sUrl), $sMessageText);

		$bSent = HelperService::sendEmail($oMember->email,
				ConfigService::getItem('support_email'),
				$sSubject,
				$sMessageText,
				$sMessageHTML);
		return $bSent;
	}

	public function sendSubaccountConfirmationEmail(MemberModel $oMember) {
		log_message('required','confirmation: '.$oMember->email.' '.$oMember->getEmailConfirmationToken());

		$ci =& get_instance();
		$sToken = $oMember->getEmailConfirmationToken();
		$sUrl = site_url('members/register_subaccount/'.$oMember->member_id).'?cfmtk='.$sToken;
		$oParentMember = $this->getParentMember($oMember)->reset();

		$sSubject = ConfigService::getItem('app_name').' - Account Invitation';

		$sMessageHTML = $ci->load->view('emails/account_invitation',array(
			'sConfirmationLink' => $sUrl,
			'sEmail'    => $oParentMember->email
		),true);

		$sMessageText = "You’ve been invited to a Contract Hound account by {$oParentMember->email}.\n".
				"Click the link below to create your password and log in to this account.\n".
				$sUrl.
				"\nIf you believe you have received this message in error, please contact customer support at support@contracthound.com";

		$bSent = HelperService::sendEmail(
				$oMember->email,
				ConfigService::getItem('support_email'),
				$sSubject,
				$sMessageText,
				$sMessageHTML
		);
		log_message('required','confirmation email sent: '. $bSent?1:0);
		return $bSent;
	}

	public function sendSubaccountAddEmail(MemberModel $oMember, MemberModel $oInviter, MemberModel $oParent) {
		log_message('required','sendSubaccountAddEmail: '.$oMember->email);

		$ci =& get_instance();
		$sUrl = site_url('members/login/');

		$sSubject = ConfigService::getItem('app_name').' - Account Invitation';

		$sMessageHTML = $ci->load->view('emails/account_add',array(
			'sParentEmail' => $oParent->email
			,'sInviterEmail' => $oInviter->email
			,'sUrl'  => $sUrl
		),true);

		$sMessageText = "You’ve been invited to a Contract Hound account ($oParent->email) by {$oInviter->email}.\n".
				"Click the link below to log in. You can access this account from Switch Account in the menu\n".
				$sUrl.
				"\nIf you believe you have received this message in error, please contact customer support at support@contracthound.com";

		$bSent = HelperService::sendEmail(
				$oMember->email,
				ConfigService::getItem('support_email'),
				$sSubject,
				$sMessageText,
				$sMessageHTML
		);
		log_message('required','sendSubaccountAddEmail email sent: '. $bSent?1:0);
		return $bSent;
	}

	/**
	 * Update Member
	 *
	 * @access public
	 * @param MemberModel $oMember
	 * @return ServiceResponse
	 */
	public function updateMember(MemberModel $oMember) {
		if (!$oMember->member_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		if ($oMember->new_password) {
			$oMember->password = $this->encryptPassword($oMember->email,$oMember->new_password);
			$oMember->removeField('new_password');
		}

		$bResponse = $this->_getModel('members_m')->updateItem($oMember->toArray());
		if ($bResponse) {
			$oMember->isSaved(true);
			return new ServiceResponse(array($oMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Encrypt Password
	 *
	 * @access protected
	 * @param string $sEmail
	 * @param string $sPassword
	 * @return string
	 */
	protected function encryptPassword($sEmail,$sPassword) {
		return md5(md5('VrEtaSu6XOi2VYno9g75f9w7sddYno2r'.$sPassword.'7tlaBGHbX5FlgLt9iNJZqUY1bbxqtt9v'.$sPassword).'nzM2luKkEP27vGAAoqCNGECMEUdS53wr'.md5('rXkITuAwieJloIqbr56SyCdpp7fUMWR4'.$sPassword));
	}
	
	public function asdfasdfasdf($sEmail,$sPassword) {
		return $this->encryptPassword($sEmail,$sPassword);
	}
}
