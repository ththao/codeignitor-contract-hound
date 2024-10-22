<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/libraries/CIMInterface.php');
/**
 * Billing Service Class
 *    Interface between the app and Authorize.net
 *
 * @access public
 */
class BillingService extends Service {

	protected $_oCIM = null;

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
		$this->_loadModelClass('BillingInfoModel');
		$this->_loadModelClass('SubscriptionModel');
		$this->_loadModelClass('SubscriptionLogModel');
	}

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	protected function _getCIM() {
		if (empty($this->_oCIM)) {
			$this->_oCIM = new CIMInterface();
		}

		return $this->_oCIM;
	}

	public function testing() {
		return $this->_getCIM()->getCustomerBasicsById(153499950);
	}

	protected function _getPlan($iType) {
		ConfigService::loadFile('billing');
		$aPlans = ConfigService::getItem('plan_details');
		if (!isset($aPlans[$iType])) {
			throw new Exception('Plan not found: '.$iType);
		}

		return $aPlans[$iType];
	}

	public function chargeSubscription(SubscriptionModel $oSubscription,$oBillingInfo=false) {
		if (empty($oBillingInfo)) {
			$oBillingInfo = Service::load('billinginfo')->getBillingInfo(array(
				'member_id' => $oSubscription->member_id
				, 'status'  => BillingInfoModel::STATUS_ACTIVE
			))->first();
		}

		$oSubscription->last_checked = date('Y-m-d H:i:s');
		if (empty($oBillingInfo)) {
			if ($oSubscription->status = SubscriptionModel::StatusActive) {
				$oSubscription->status = SubscriptionModel::StatusSuspended;
			}

			return Service::load('subscription')->updateSubscription($oSubscription);
		}

		$aPlan = $this->_getPlan($oSubscription->type);
		$oLog = Service::load('subscriptionlog')->addLog(new SubscriptionLogModel(array(
			'member_id'             => $oSubscription->member_id
			,'subscription_id'      => $oSubscription->subscription_id
			,'response_code'        => SubscriptionLogModel::ResponseCodePending
			,'response_reason_code' => 0
			,'message'              => 'Pending Submission'
			,'create_date'          => date('Y-m-d H:i:s')
			,'amount'               => $aPlan['price']
		)))->first();

		$aResult = $this->_getCIM()->chargeCustomer(
			$oLog->subscription_log_id
			,$oBillingInfo->cim_profile_id
			,$oBillingInfo->cim_payment_id
			,$oSubscription->type
			,'SEO Alarms Subscription'
			,'SEO Alarms '.$aPlan['name'].' Subscription'
			,$aPlan['price']
		);

		$oLog->status = SubscriptionLogModel::ResponseCodeApproved;
		if (empty($aResult['success'])) {
			$oSubscription->status = SubscriptionModel::StatusSuspended;

			$oLog->message = $aResult['error_message'];
			$oLog->response_code = SubscriptionLogModel::ResponseCodeDeclined;
			$oLog->response_reason_code = $aResult['error_code'];
		} else {
			$sOneMonth = date('Y-m-d H:i:s',strtotime('+1 month'));
			$oSubscription->expire_date = $sOneMonth;
			$oSubscription->next_billing_date = $sOneMonth;
			$oSubscription->price = $aPlan['price'];

			$oLog->response_code = SubscriptionLogModel::ResponseCodeApproved;
			$oLog->message = '';
		}

		Service::load('subscription')->updateSubscription($oSubscription);
		return Service::load('subscriptionlog')->updateLog($oLog);
	}

	public function adjustSubscription(SubscriptionModel $oOrig, SubscriptionModel $oNew, $oBillingInfo) {
		if ($oOrig->type == $oNew->type) {
			return new SubscriptionLogModel(array('status'=>SubscriptionLogModel::ResponseCodeApproved));
		}

		$aOrigPlan = $this->_getPlan($oOrig->type);
		$aNewPlan = $this->_getPlan($oNew->type);

		$iDiff = $aNewPlan['price'] - $aOrigPlan['price'];
		$oLog = Service::load('subscriptionlog')->addLog(new SubscriptionLogModel(array(
			'member_id'             => $oNew->member_id
			,'subscription_id'      => $oNew->subscription_id
			,'response_code'        => SubscriptionLogModel::ResponseCodePending
			,'response_reason_code' => 0
			,'message'              => 'Pending Submission'
			,'create_date'          => date('Y-m-d H:i:s')
			,'amount'               => $iDiff
		)))->first();

		$aResult = $this->_getCIM()->chargeCustomer(
			$oLog->subscription_log_id
			,$oBillingInfo->cim_profile_id
			,$oBillingInfo->cim_payment_id
			,$oNew->type
			,'SEO Alarms Subscription'
			,'SEO Alarms Subscription Adjustment'
			,$iDiff
		);

		$oLog->status = SubscriptionLogModel::ResponseCodeApproved;
		$oLog->message = '';
		if (empty($aResult['success'])) {
			$oLog->message = $aResult['error_message'];
			$oLog->response_code = SubscriptionLogModel::ResponseCodeDeclined;
			$oLog->response_reason_code = $aResult['error_code'];
		}

		return Service::load('subscriptionlog')->updateLog($oLog);
	}

	public function createSubscription(BillingInfoModel $oInfo) {
		$aPlan = $this->_getPlan($oInfo->subscription_type);
		$oInfo->title = 'SEO Alarms Subscription';
		$oInfo->description = 'SEO Alarms '.$aPlan['name'].' Subscription';
		$oInfo->price = $aPlan['price'];

		$oLog = Service::load('subscriptionlog')->addLog(new SubscriptionLogModel(array(
			'member_id'             => $oInfo->member_id
			,'subscription_id'      => 0
			,'response_code'        => SubscriptionLogModel::ResponseCodePending
			,'response_reason_code' => 0
			,'message'              => 'Pending Submission'
			,'create_date'          => date('Y-m-d H:i:s')
			,'amount'               => $aPlan['price']
		)))->first();

		$oInfo->invoice_id = $oLog->subscription_log_id;

		$mResponse = $this->_getCIM()->createAndCharge($oInfo->allToArray());
		if (empty($mResponse['success'])) {
			log_message('required','createSubscription createAndCharge failed');
			if (!empty($mResponse['profile_id'])) {
				log_message('required','createSubscription profile_id not empty');
				$oCreate = $this->_addCIMToBillingInfo($oInfo,$mResponse);
				$oCreate->setStatus(ServiceResponse::StatusError);

				$oCreate->setError('Unable to charge credit card.');
				if (!empty($mResponse['error_message'])) {
					$oCreate->sError = $mResponse['error_message'];
				}

				$oLog->message = $mResponse['error_message'];
				$oLog->response_code = SubscriptionLogModel::ResponseCodeDeclined;
				$oLog->response_reason_code = $mResponse['error_code'];
				Service::load('subscriptionlog')->updateLog($oLog);

				return $oCreate;
			}

			$oLog->message = $mResponse['error_message'];
			$oLog->response_code = SubscriptionLogModel::ResponseCodeDeclined;
			$oLog->response_reason_code = $mResponse['error_code'];
			Service::load('subscriptionlog')->updateLog($oLog);

			return $this->_setupErrorResponse();
		}

		$oLog->response_code = SubscriptionLogModel::ResponseCodeApproved;
		$oLog->message = 'Charged successful.';
		$oLog->trans_id = $mResponse['transaction_id'];
		Service::load('subscriptionlog')->updateLog($oLog);

		$sOneMonth = date('Y-m-d H:i:s',strtotime('+1 month'));
		$sDate = date('Y-m-d H:i:s');

		log_message('required','createSubscription createAndCharge passed');
		$this->_addCIMToBillingInfo($oInfo,$mResponse);
		return Service::load('subscription')->addSubscription(new SubscriptionModel(array(
			'member_id'          => $oInfo->member_id
			,'type'              => $oInfo->subscription_type
			,'status'	         => SubscriptionModel::StatusActive
			,'price'             => $oInfo->price
			,'start_date'        => $sDate
			,'last_checked'      => $sDate
			,'next_billing_date' => $sOneMonth
		)));
	}

	protected function _addCIMToBillingInfo(BillingInfoModel $oInfo,$mResults) {
		log_message('required','_addCIMToBillingInfo merging shit');
		$oInfo->cim_profile_id = $mResults['profile_id'];
		$oInfo->cim_payment_id = $mResults['payment_profile_id'];

		if ($oInfo->billing_info_id) {
			log_message('required','_addCIMToBillingInfo updateBillingInfo');
			return Service::load('billinginfo')->updateBillingInfo($oInfo);
		}

		log_message('required','_addCIMToBillingInfo addBillingInfo');
		return Service::load('billinginfo')->addBillingInfo($oInfo);
	}

	public function updateBilling(BillingInfoModel $oInfo) {
		$bOk = $this->_getCIM()->updateBilling(
			$oInfo->cim_profile_id
			,$oInfo->cim_payment_id
			,$oInfo->first_name
			,$oInfo->last_name
			,$oInfo->address
			,$oInfo->city
			,$oInfo->state
			,$oInfo->zip
			,$oInfo->country
			,$oInfo->cc_number
			,$oInfo->cc_expire_year.'-'.$oInfo->cc_expire_month
		);

		if ($bOk) {
			return Service::load('billinginfo')->updateBillingInfo($oInfo);
		}

		return $this->_setupErrorResponse();
	}
}
