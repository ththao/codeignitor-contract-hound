<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	protected $aData = array();
	protected $_iMemberId = null;
	protected $_iParentId = null;
	protected $_sLayout = 'layout';
	protected $aPartials = array();
	protected $_aPagesVisited = array();

	protected $aAdditionalCSSFiles = array();
	protected $aAdditionalJS = array();
	protected $aAdditionalJSFiles = array();

	protected $locale;
	protected $cTimeZone;
	protected $cDateFormat;
	public function __construct() {
		parent::__construct();

		if ($this->session->userdata('member_pages_visited')) {
			$this->_aPagesVisited = unserialize($this->session->userdata('member_pages_visited'));
		}

		if ($this->session->userdata('member_id')) {
			$this->_iMemberId = $this->session->userdata('member_id');
		}

		if ($this->session->userdata('member_parent_id')) {
			$this->_iParentId = $this->session->userdata('member_parent_id');
		}

		$this->_loadExtras();
	}

	public function set($sKey,$mValue) {
		return $this->aData[$sKey] = $mValue;
	}

	protected function _isAdmin() {
		if (empty($_SESSION['admin_member_id']) && (empty($this->_iMemberId) || !in_array($this->_iMemberId, $this->config->item('admin_ids')))) {
			return false;
		}

		return true;
	}

	public function build($sTemplate) {
		$this->_loadExtras();

		$this->load->view($this->_sLayout.'/header',array_merge($this->aData,array('aAdditionalCSSFiles'=>$this->aAdditionalCSSFiles)));

		$this->load->view($sTemplate,$this->aData);

		foreach ($this->aPartials as $sPartial) {
			$this->load->view('partials/'.$sPartial,$this->aData);
		}

		$this->load->view($this->_sLayout.'/footer2',array_merge($this->aData,array('aAdditionalJSFiles'=>$this->aAdditionalJSFiles)));

		foreach ($this->aAdditionalJS as $sAdditionalJS) {
			$this->load->view('partials/'.$sAdditionalJS,$this->aData);
		}

		$this->load->view($this->_sLayout.'/close_page',$this->aData);
	}

	protected function _loadExtras() {
		$this->set('iCurrentlyLoggedInMemberId', $this->_iMemberId);
		$this->set('iCurrentlyLoggedInParentId', $this->_iParentId);
		$this->set('iCurrentMemberId',$this->session->userdata('member_id'));
		$this->set('sCurrentMemberEmail',$this->session->userdata('member_email'));

		$bCurrentMemberIsAccountOwner = false;
		if ($this->_iMemberId == $this->_iParentId) {
			$bCurrentMemberIsAccountOwner = true;
		}
		$this->set('bCurrentMemberIsAccountOwner',$bCurrentMemberIsAccountOwner);

		if (in_array($this->_iMemberId,$this->config->item('admin_ids'))) {
			$this->set('bCurrentlyLoggedInMemberIsAdmin',true);
		} else {
			$this->set('bCurrentlyLoggedInMemberIsAdmin',false);
		}
		$oResponse = Service::load('member')->getMember(array(
			'member_id' => $this->_iMemberId
		))->first();
		if($oResponse->country_id != null){
			//Get current country config
			$oCountry = Service::load('country')->getCountries(['id' => $oResponse->country_id,'status' => 1], '')->first();
			//Set locale and timezone
			if($oCountry->date_format != null && $oCountry->time_zone != null) {
				//$isLocaleSet = setlocale(LC_TIME, $oCountry->locale);
                //$this->set('locale',$oCountry->locale);
                $this->set('time_zone',$oCountry->time_zone);
                $this->set('date_format',$oCountry->date_format);
                $this->cTimeZone = $oCountry->time_zone;
                $this->cDateFormat = $oCountry->date_format;
                define('LOCAL_DATE_FORMAT',$this->cDateFormat);
			}else{
				//setlocale(LC_TIME, 'en-US');
                //$this->set('locale','en');
                $this->set('time_zone','America/New_York');
                $this->set('date_format','m/d/Y');
                $this->cTimeZone = 'America/New_York';
                $this->cDateFormat = 'm/d/Y';
                define('LOCAL_DATE_FORMAT','m/d/Y');
			}

		}else{
            $this->set('time_zone','America/New_York');
            $this->set('date_format','m/d/Y');
            $this->cTimeZone = 'America/New_York';
            $this->cDateFormat = 'm/d/Y';
            define('LOCAL_DATE_FORMAT','m/d/Y');
        }
        if ($oResponse->currency) {
            $currency = Service::load('country')->getCountries(['currency' => $oResponse->currency,'status' => 1], '')->first();
            if ($currency) {
                $this->set('sCurrency', $currency->currency_symbol ? $currency->currency_symbol : $currency->currency);
            } else {
                $this->set('sCurrency',$oResponse->currency);
            }
        } else {
            $this->set('sCurrency','$');
        }
		$this->_loadSidebarNotifications();
		$this->_loadMenuOtherAccounts();
	}

	protected function _loadMenuOtherAccounts() {
		if (empty($this->_iMemberId)) {
			return true;
		}

		$sLastPull = $this->session->userdata('members_last_other_account_pull',false);
		if (empty($iLastPull) || strtotime($sLastPull) < strtotime('-3 minutes')) {
			$oOtherMemberAccounts = Service::load('othermemberaccount')->getOtherMemberAccountsWithParentAccountData(array(
				'other_member_accounts.member_id' => $this->_iMemberId
			));

			//log_message('required','oOtherMemberAccounts '.print_r($oOtherMemberAccounts,true));
			//log_message('required','oOtherMemberAccounts->count '.$oOtherMemberAccounts->count);
			$this->session->set_userdata('members_has_other_accounts',$oOtherMemberAccounts->count > 0);
			$this->session->set_userdata('members_last_sidebar_notifications_pull',date('Y-m-d H:i:s'));
			$bHasAccess = $oOtherMemberAccounts->count > 0;
		} else {
			$bHasAccess = $this->session->set_userdata('members_has_other_accounts');
		}

		//log_message('required','bCurrentMemberHasAccessToOtherAccounts '.($bHasAccess?1:0));
		$this->set('bCurrentMemberHasAccessToOtherAccounts',$bHasAccess);
	}

	protected function _loadSidebarNotifications() {
		if (empty($this->_iParentId)) {
			return true;
		}

		$sLastPull = $this->session->userdata('members_last_sidebar_notifications_pull',false);
		if (empty($iLastPull) || strtotime($sLastPull) < strtotime('-3 minutes')) {
			$oLogs = Service::load('contractlog')->getContractLogsByTeamMember($this->_iMemberId,$this->_iParentId);
			//$aLogs = array_reverse($oLogs->getResults());
			$aLogs = $oLogs->getResults();
			unset($oLogs);

			$aMemberIds = array();
			foreach ($aLogs as $oLog) {
				if ($oLog->member_id) {
					$aMemberIds[$oLog->member_id] = $oLog->member_id;
				}
			}

			if (!empty($aMemberIds)) {
				$oMembers = Service::load('member')->getMembers(array('member_id'=>$aMemberIds));
				$aMembers = array();
				foreach ($oMembers as $oMember) {
					$aMembers[$oMember->member_id] = $oMember;
				}
				unset($oMembers);

				foreach ($aLogs as $iIndex=>$oLog) {
					if (isset($aMembers[$oLog->member_id])) {
						$oLog->member = $aMembers[$oLog->member_id];
						$aLogs[$iIndex] = $oLog;
					}
				}
			}

			$this->session->set_userdata('members_sidebar_notifications',serialize($aLogs));
			$this->session->set_userdata('members_last_sidebar_notifications_pull',date('Y-m-d H:i:s'));
			$this->set('aSidebarNotifications',$aLogs);
		} else {
			$sLogs = $this->session->set_userdata('members_sidebar_notifications');
			$aLogs = @unserialize($sLogs);
			if (!empty($aLogs)) {
				$this->set('aSidebarNotifications',$aLogs);
			} else {
			    $this->set('aSidebarNotifications',[]);
			}
		}

		return true;
	}

	/**
	 * Is Post
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _isPost() {
		if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			return true;
		} elseif (!empty($_POST)) {
			return true;
		} else {
			return false;
		}
	}
}

class Admin_Controller extends MY_Controller {
	protected $_iAdminId = null;

	public function __construct() {
		parent::__construct();

		if (!in_array($this->_iMemberId,$this->config->item('admin_ids'))) {
			log_message('error','not admin: '.$this->_iMemberId);
			redirect('members/login');
			return;
		}
	}

	public function build($sTemplate) {
		$this->_loadExtras();

		$this->load->view('layout/admin_header',$this->aData);
		$this->load->view($sTemplate,$this->aData);
		$this->load->view('layout/footer',$this->aData);
	}

	protected function _loadExtras() {
		parent::_loadExtras();
		$this->set('iCurrentlyLoggedInAdminId',$this->_iAdminId);
		$this->set('bCurrentlyLoggedInMemberIsAdmin',true);
	}
}

class User_Controller extends MY_Controller {
	protected $bCurrentlyLoggedInMemberIsAdmin = false;

	protected $_aIngoredPages = array(
		'members/login'
		,'login/index'    // for /login

		,'members/init'

		,'members/logout'
		,'logout/index'   // for /logout

		,'members/register'
		,'register/index' // for /register

		,'members/register_later'
		,'members/request_reset_password'

		,'members/confirm'
		,'members/register_subaccount'
		,'members/confirm_reset_password'

		,'members/reset_password'
		,'members/resend_confirmation_token'
		,'members/confirm_reset_password_direct'

		,'contact/contact_us'
		,'contact/custom_quote'

		,'cron/health'
	);

	public function __construct() {
		parent::__construct();

		$sCurrentPage = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, 'index');

		// Cron proccess
		$bIsCron = strpos($sCurrentPage,'cron_') !== false || strpos($sCurrentPage,'cron/') === 0;
		$sAdminKey = $this->config->item('admin_key');
		$sRequestAdminKey = !empty($_GET['admin_key'])?$_GET['admin_key']:'';
		$bHasAdminKey = (!empty($sAdminKey) && !empty($sRequestAdminKey) && strcmp($sAdminKey,$sRequestAdminKey) === 0);
		$bCronBypass = $bIsCron && $bHasAdminKey;

		if (!$bCronBypass && !$this->_iMemberId && !in_array($sCurrentPage,$this->_aIngoredPages)) {
			$sRemoteIp = '?';
			if (!empty($_SERVER['HTTP_USER_AGENT'])) {
				if (strpos($_SERVER['HTTP_USER_AGENT'],'Monit')!==false) {
					redirect('members/login');
					return;
				}

				$sRemoteIp = $_SERVER['HTTP_USER_AGENT'];
			}

			if (!empty($_SERVER['REMOTE_ADDR'])) {
				$sRemoteIp = $_SERVER['REMOTE_ADDR'];
			}

			if (!empty($_SERVER['CI_CRON'])) {
				$sRemoteIp = 'croncli';
			}

			//log_message('error','invalid access request to :'.(!empty($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:$sCurrentPage).' AKL '.$sAdminKey.' sak: '.$sRequestAdminKey.' hak: '.print_r($bHasAdminKey,true).' '.($bCronBypass?'cronbypassactive':'cronbypassfailed').' RI: '.$sRemoteIp);
			//log_message('error','invalid access request server :'.print_r($_SERVER,true));
			redirect('members/login');
			return;
		}

		$this->bCurrentlyLoggedInMemberIsAdmin = false;
		if (in_array($this->_iMemberId,$this->config->item('admin_ids'))) {
			$this->bCurrentlyLoggedInMemberIsAdmin = true;
			$this->set('bCurrentlyLoggedInMemberIsAdmin',true);
		} else {
			$this->set('bCurrentlyLoggedInMemberIsAdmin',false);
		}

		$sLastPage = end($this->_aPagesVisited);
		$sRequestedPage = trim($_SERVER['REQUEST_URI'],'/');
		if (empty($sLastPage) || (!empty($sLastPage) && strcmp($sLastPage,$sRequestedPage) !== 0)) {
			array_push($this->_aPagesVisited,trim($_SERVER['REQUEST_URI'],'/'));
			//log_message('required',$this->_iMemberId.' new current page: '.$sCurrentPage.' pages: '.print_r($this->_aPagesVisited,true));
			if (count($this->_aPagesVisited) > 8) {
				$this->_aPagesVisited = array_slice($this->_aPagesVisited,1,9);
			}
		}

		$this->session->set_userdata('member_pages_visited',serialize($this->_aPagesVisited));
	}

	protected function _redirectLast($sDefault = '') {
		$sCurrentPage = array_shift($this->_aPagesVisited);

		if (count($this->_aPagesVisited)) {
			$sLastPage = array_shift($this->_aPagesVisited);
			redirect($sLastPage);
		}

		if (!empty($sDefault) && !empty($sCurrentPage)) {
			array_push($this->_aPagesVisited, $sCurrentPage);
			redirect($sDefault);
		}

		if (empty($sCurrentPage)) { $sCurrentPage = 'welcome'; }
		redirect($sCurrentPage);
	}

	protected function _getLastPage() {
		if (count(($this->_aPagesVisited)) > 1) {
			end($this->_aPagesVisited);
			return prev($this->_aPagesVisited);
		}

		return null;
	}

	/**
	 * Get relevant subscription
	 *
	 * @access protected
	 * @return SubscriptionModel
	 */
	protected function _getSubscription() {
		// not logged in
		if (!$this->session->userdata('member_parent_id')) {
			return new SubscriptionModel();
		}

		$sLastLoaded = $this->session->userdata('member_subscription_last_load');
		if (empty($sLastLoaded) || strtotime($sLastLoaded) < strtotime('-2 hours')) {
			$this->session->unset_userdata('member_subscription_last_load');
			$this->session->unset_userdata('member_current_subscription');
		}

		// already found and stored
		if ($this->session->userdata('member_current_subscription')) {
			$mSubscription = $this->session->userdata('member_current_subscription');
			$oSubscription = unserialize($mSubscription);
			return $oSubscription;
		}

		// figure out current
		$oSubscriptionsResponse = Service::load('subscription')->getSubscriptions(array(
			'member_id' => $this->_iParentId
			/*,'status'   => array(
				SubscriptionModel::StatusActive
				,SubscriptionModel::StatusFree
				,SubscriptionModel::StatusTrial
			)*/
		),'status desc, contract_limit desc');
		//log_message('required',$this->_iMemberId.' / sub count / '.$oSubscriptionsResponse->count);
		//log_message('required',$this->_iMemberId.' / subs found / '.$oSubscriptionsResponse);

		if (!$oSubscriptionsResponse->count()) {
			$oSub = Service::load('subscription')->addSubscription(new SubscriptionModel(array(
				'member_id'       => $this->session->userdata('member_id')
				,'create_date'    => date('Y-m-d H:i:s')
				,'plan_id'        => 0
				,'status'         => SubscriptionModel::StatusTrial
				,'approvals'      => SubscriptionModel::APPROVALS_ENABLED
				,'contract_limit' => 50
				,'price'          => 0.00
				,'start_date'     => date('Y-m-d H:i:s')
				,'expire_date'    => date('Y-m-d H:i:s',strtotime('+15 days'))
				,'last_checked'   => date('Y-m-d H:i:s')
				,'last_changed'   => date('Y-m-d H:i:s')
			)))->first();

			$this->session->set_userdata('member_current_subscription',serialize($oSub));
			$this->session->set_userdata('member_subscription_last_load',date('Y-m-d H:i:s'));

			return $oSub;
		}

		//$oMaxActive = null;
		//$oMostRecentNonActive = null;

		foreach ($oSubscriptionsResponse as $oSubscription) {
			log_message('required',$this->_iMemberId.' / checking sub / '.$oSubscription->subscription_id.' active: '.($oSubscription->isActive()?'yes':'no').' type: '.$oSubscription->type);

			if ($oSubscription->isActive() && (empty($oMaxActive) || $oMaxActive->type < $oSubscription->type)) {
				log_message('required',$this->_iMemberId.' / new max sub / '.$oSubscription->subscription_id);
				$oMaxActive = $oSubscription;
			}

			if (!$oSubscription->isActive() && (empty($oMostRecentNonActive) || strtotime($oMostRecentNonActive->expire_date) < strtotime($oSubscription->expire_date))) {
				log_message('required',$this->_iMemberId.' / new most recent sub / '.$oSubscription->subscription_id);
				$oMostRecentNonActive = $oSubscription;
			}
		}

		// active member
		if (!empty($oMaxActive)) {
			$this->session->set_userdata('member_current_subscription',serialize($oMaxActive));
			$this->session->set_userdata('member_subscription_last_load',date('Y-m-d H:i:s'));
			log_message('required',$this->_iMemberId.' / max sub / '.$oMaxActive->subscription_id);
			return $oMaxActive;
		}

		// not active member
		if (!empty($oMostRecentNonActive)) {
			$this->session->set_userdata('member_current_subscription',serialize($oMostRecentNonActive));
			$this->session->set_userdata('member_subscription_last_load',date('Y-m-d H:i:s'));

			$this->session->set_userdata('member_constant_error',$this->lang->line('subscription_expired_notice'));
			log_message('required',$this->_iMemberId.' / recent sub / '.$oMostRecentNonActive->subscription_id);
			return $oMostRecentNonActive;
		}

		// Free account
		$oSub =  new SubscriptionModel();
		$this->session->set_userdata('member_current_subscription',serialize($oSub));
		$this->session->set_userdata('member_subscription_last_load',date('Y-m-d H:i:s'));

		return $oSub;
	}

	/**
	 * Get relevant subscription
	 *
	 * @access protected
	 * @return SubscriptionModel
	 */
	protected function _getSubscriptionByMemberId($iMemberId = null) {
		// not logged in
		if (!$iMemberId) {
			return new SubscriptionModel();
		}

		$oMember = Service::load('member')->getMember(array('member_id' => $iMemberId))->reset();
		if (empty($oMember)) {
			return new SubscriptionModel();
		}

		if ($oMember->member_id != $oMember->parent_id) {
			$iMemberId = $oMember->parent_id;
		}

		// figure out current
		$oSubscriptionsResponse = Service::load('subscription')->getSubscriptions(array(
			'member_id' => $iMemberId
			/*,'status'   => array(
				SubscriptionModel::StatusActive
				,SubscriptionModel::StatusFree
				,SubscriptionModel::StatusTrial
			)*/
		),'status desc, contract_limit desc');
		//log_message('required',$iMemberId.' / sub count / '.$oSubscriptionsResponse->count);
		//log_message('required',$this->_iMemberId.' / subs found / '.$oSubscriptionsResponse);

		if (!$oSubscriptionsResponse->count()) {
			return new SubscriptionModel();
		}

		//$oMaxActive = null;
		//$oMostRecentNonActive = null;

		foreach ($oSubscriptionsResponse as $oSubscription) {
			//log_message('required',$iMemberId.' / checking sub / '.$oSubscription->subscription_id.' active: '.($oSubscription->isActive()?'yes':'no').' type: '.$oSubscription->type);

			if ($oSubscription->isActive() && (empty($oMaxActive) || $oMaxActive->type < $oSubscription->type)) {
				//log_message('required',$iMemberId.' / new max sub / '.$oSubscription->subscription_id);
				$oMaxActive = $oSubscription;
			}

			if (!$oSubscription->isActive() && (empty($oMostRecentNonActive) || strtotime($oMostRecentNonActive->expire_date) < strtotime($oSubscription->expire_date))) {
				//log_message('required',$iMemberId.' / new most recent sub / '.$oSubscription->subscription_id);
				$oMostRecentNonActive = $oSubscription;
			}
		}

		// active member
		if (!empty($oMaxActive)) {
			//log_message('required',$iMemberId.' / max sub / '.$oMaxActive->subscription_id);
			return $oMaxActive;
		}

		// not active member
		if (!empty($oMostRecentNonActive)) {
			//log_message('required',$iMemberId.' / recent sub / '.$oMostRecentNonActive->subscription_id);
			return $oMostRecentNonActive;
		}

		// Free account
		$oSub =  new SubscriptionModel();
		$this->session->set_userdata('member_current_subscription',serialize($oSub));
		$this->session->set_userdata('member_subscription_last_load',date('Y-m-d H:i:s'));

		return $oSub;
	}

	protected function _setupPagination($iLimit,$iTotal,$iOffset,$sBaseUrl) {
		$this->load->library('pagination');

		$aConfig = $this->config->item('pagination_settings');

		$aConfig['base_url']             = $sBaseUrl.'?l='.$iLimit;
		$aConfig['total_rows']           = $iTotal;
		$aConfig['cur_page']             = $iOffset / $iLimit;
		$aConfig['per_page']             = $iLimit;

		$this->pagination->initialize($aConfig);
		return true;
	}

	protected function _getIntUrlParam($sField,$iDefault) {
		$iNum = $this->input->my_get($sField,$iDefault);

		if (!is_numeric($iNum) || !preg_match('/^[0-9]+$/', $iNum)) {
			$iNum = $iDefault;
		}

		return $iNum;
	}

	protected function _getOffset($iDefault=0) {
		return $this->_getIntUrlParam('o',$iDefault);
	}

	protected function _getLimit($iDefault=100) {
		return $this->_getIntUrlParam('l',$iDefault);
	}

	protected function _loadExtras() {
		parent::_loadExtras();
		if (!empty($this->_iParentId)) {
			$this->_setupContractSidebar();
			$this->_setupBoardSidebar();
			$this->set('oCurrentParentSub',$this->_getSubscription());
		}
	}

	protected function _setupContractSidebar() {
		$oContracts = Service::load('contract')->searchContracts($this->_iMemberId,$this->_iParentId,'',null,
			'cs.create_date desc',$this->_getLimit(5));
		/*$oContracts = Service::load('contract')->getContracts(array(
			'owner_id' => $this->_iParentId
			,'status'  => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		),'create_date desc',$this->_getLimit(5));*/

		$this->set('oContractsSidebar',$oContracts);
		return true;
	}

	protected function _setupBoardSidebar() {
		$oBoards = Service::load('board')->getBoards(array(
			'parent_id' => $this->_iParentId,
		    'parent_board_id IS NULL' => null
		), 'create_date desc', $this->_getLimit(5));

		$this->set('oBoardsSidebar',$oBoards);
		return true;
	}
}
