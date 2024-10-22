<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Billing extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////////
	///  Super Methods   /////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function __construct() {
		parent::__construct();
		
		if ($this->_iMemberId != $this->_iParentId) {
			redirect('welcome');
		}

		ConfigService::loadFile('billing');

		$this->load->library('form_validation');
		$this->load->helper('stripe');
	}

	///////////////////////////////////////////////////////////////////////////
	///  Methods   ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function test_key() {
		echo "<pre>\n";
		echo ConfigService::getItem('stripe_private_key')."\n";
		var_dump($_SERVER);
		echo ENVIRONMENT;
	}

	/**
	 * Main view contracts
	 *
	 * @access public
	 */
	public function index() {
		$oSub = $this->_getSubscription();
		//$aBillingConfig = ConfigService::getItem('plan_details');
		$aPlanDetails = array();

		$iPlanCount = Service::load('contract')->getContractCount(array(
			'parent_id' => $this->_iParentId,
			'status !=' => ContractModel::STATUS_DELETED
		))->total;
		$oBillingInfos = Service::load('billinginfo')->getBillingInfos(array('member_id'=>$this->_iMemberId));
		$oBillingLogs = Service::load('billinglog')->getBillingLogs(array('member_id'=>$this->_iMemberId),'create_date desc',50);
		$oMember = Service::load('member')->getMember(array('member_id'=>$this->_iMemberId))->first();

		$this->set('oMember',$oMember);
		$this->set('aPlanDetails',$aPlanDetails);
		$this->set('oBillingInfos',$oBillingInfos);
		$this->set('oBillingLogs',$oBillingLogs);
		$this->set('oSub',$oSub);
		$this->set('iPlanCount',$iPlanCount);
		$this->set('sHeader','Billing');
		$this->build('billing/index');
	}

	public function near_limit() {
		$oSub = $this->_getSubscription();
		$aPlans = ConfigService::getItem('plans');
		$iPlanCount = Service::load('contract')->getContractCount(array('parent_id'=>$this->_iParentId))->total;

		$this->aData['aPlans'] = $aPlans;
		$this->aData['oSub'] = $oSub;
		$this->aData['iPlanCount'] = $iPlanCount;
		$this->load->view('billing/near-limit',$this->aData);
	}

	public function limited_access() {
		$oMember = Service::load('member')->getMember(array('member_id'=>$this->_iMemberId))->first();
		if($oMember->parent_id != $oMember->member_id) {
			$oMember = Service::load('member')->getMember(array('member_id'=>$oMember->parent_id))->first();
		}
		
		$this->aData['sParentEmail'] = $oMember->email;
		$this->load->view('billing/contact_account_owner',$this->aData);
	}

	public function trial_expired() {
	    send_analytic_event('Trial Expired', null, null);
	    
		$this->load->view('billing/trial_expired',$this->aData);
	}
	
	public function upgrade() {
	    $oBillingInfo = Service::load('billinginfo')->getBillingInfos(array('member_id'=>$this->_iMemberId,'status'=>BillingInfoModel::STATUS_ACTIVE))->reset();
	    $oSub = $this->_getSubscription();
	    
	    $this->aData['oBillingInfo'] = $oBillingInfo;
	    $this->aData['oSub'] = $oSub;
	    
	    $oMember = Service::load('member')->getMember(array('member_id'=>$this->_iMemberId))->first();
	    
	    $this->set('oMember', $oMember);
	    $this->set('oPrices', ConfigService::getItem('plans'));
	    
	    $this->load->view('billing/upgrade_checkout',$this->aData);
	}
	
	public function create_checkout_session() {
	    $oMember = Service::load('member')->getMember(array('member_id'=>$this->_iMemberId))->first();
	    
	    $checkout_session = checkout($_POST['priceId'], $oMember->email);
	    
	    echo json_encode(['sessionId' => $checkout_session['id']]);
	}
	
	public function stripe_checkout_success() {
	    $oMember = Service::load('member')->getMember(array('member_id'=>$this->_iMemberId))->first();
	    if (empty($oMember)) {
	        $this->session->error('Member lookup failed');
	        redirect('billing');
	        return false;
	    }
	    
	    $checkout_session = retrieve_session($_GET['session_id']);
	    if (!$checkout_session) {
	        $this->session->error('Something went wrong.  Please try again. Error: STM');
	        redirect('billing/upgrade');
	        return false;
	    }
	    
	    $amount_total = $checkout_session->amount_total;
	    $aPlans = ConfigService::getItem('plans');
	    
	    $iPlanId = false;
		foreach ($aPlans as $planId => $aPlan) {
		    if ($amount_total/100 == $aPlan['price']) {
		        $iPlanId = $planId;
		        break;
			}
		}
		
		if ($iPlanId === false) {
			$this->session->error('Plan not found. (Error: NM)');
			log_message('error','Plan not found.');
			redirect('billing/upgrade');
			return false;
		}

		try {
			if (!$oMember->stripe_id) {
			    log_message('required','new customer: '.$checkout_session->customer);

				$oMember->stripe_id = $checkout_session->customer;
				Service::load('member')->updateMember($oMember);
				
				$sources = retrieve_payments($checkout_session->customer);
				if ($sources) {
				    $payment = $sources->data[0];
				    $this->_saveCreditCardFromStripeCard($payment);
				}
			}
            
			$oSub = retrieve_subscription($checkout_session->subscription);
			log_message('required','new sub: '.print_r($oSub,true));
			
			$oDBSub = $this->_saveSubscriptionFromStripe($oSub, $iPlanId);
			log_message('required','db\subscription => '.print_r($oDBSub,true));

			Service::load('subscriptionlog')->addLog(new SubscriptionLogModel(array(
				'member_id'        => $this->_iMemberId
				,'subscription_id' => $oDBSub->subscription_id
			    ,'stripe_id'       => $oSub->id
				,'status'          => $oSub->status
				,'message'         => 'Subscription Created'
				,'create_date'     => date('Y-m-d H:i:s')
				,'amount'          => $oDBSub->price
			)));
			
			$this->session->success('Subscription upgraded.');
			redirect('billing');

		} catch (Exception $e) {
			$error = $e->getMessage();
			$this->session->error($error);
			log_message('error','Billing::add_subscription caught: '.$e->getMessage());
		}

		redirect('billing/upgrade');
	}
	
	public function stripe_checkout_canceled() {
	    $this->session->error('You have canceled from Stripe checkout form.  Please try again.');
	    
	    redirect('billing/upgrade');
	    return false;
	}
	
	public function update_subscription() {
	    $oMember = Service::load('member')->getMember(array('member_id'=>$this->_iMemberId))->first();
	    if (empty($oMember)) {
	        $this->session->error('Member lookup failed');
	        redirect('billing');
	        return false;
	    }
	    
	    $oSub = $this->_getSubscription();
	    if ($oSub->status != SubscriptionModel::StatusActive || !$oSub->stripe_id) {
	        $this->session->error('Plan can not be upgraded.');
	        log_message('error','can not upgrade sub: '.$oSub->subscription_id);
	        redirect('billing/upgrade');
	    }
	    
	    if (empty($_POST['plan_id']) || !is_numeric($_POST['plan_id'])) {
	        $this->session->error('Plan not found. (Error: NAN)');
	        redirect('billing/upgrade');
	        return false;
	    }
	    
	    $iRequestedPlanId = trim($_POST['plan_id']);
	    
	    $aPlans = ConfigService::getItem('plans');
	    $iPlanId = false;
	    foreach ($aPlans as $iCurrentPlanId=>$aPlan) {
	        if ($iRequestedPlanId == $aPlan['label']) {
	            $iPlanId = $iCurrentPlanId;
	        }
	    }
	    
	    if ($iPlanId === false) {
	        $this->session->error('Plan not found. (Error: NM)');
	        log_message('error','plan_id is false, '.print_r($iRequestedPlanId,true));
	        redirect('billing/upgrade');
	        return false;
	    }
	    
	    //$iStripePlanId = $iPlanId;
	    $iStripePlanId = $aPlans[$iPlanId]['stripe_plan_id'];
	    if (!empty($_POST['approvals'])) {
	        $iStripePlanId = $aPlans[$iPlanId]['label'].'wa';
	    }
	    
	    $oStripeSubscription = retrieve_subscription($oSub->stripe_id);
	    if ($oStripeSubscription) {
	        $oStripeSubscription->plan = $iStripePlanId;
	        $oStripeSubscription->save();
	    }
	    
	    $oSub->plan_id = $iPlanId;
	    $oSub->contract_limit = $aPlans[$iPlanId]['label'];
	    $oSub->price = $aPlans[$iPlanId]['price'] + (!empty($_POST['approvals'])?250:0);
	    $oSub->approvals = SubscriptionModel::APPROVALS_ENABLED;
	    $oSub->last_changed = date('Y-m-d H:i:s');
	    Service::load('subscription')->updateSubscription($oSub);
	    
	    Service::load('subscriptionlog')->addLog(new SubscriptionLogModel(array(
	        'member_id'        => $oSub->member_id
	        ,'subscription_id' => $oSub->subscription_id
	        ,'stripe_id'       => $oStripeSubscription->id
	        ,'status'          => $oStripeSubscription->status
	        ,'message'         => 'Subscription Upgraded'
	        ,'create_date'     => date('Y-m-d H:i:s')
	        ,'amount'          => $oSub->price
	    )));
	    
	    $this->session->set_userdata('member_current_subscription',serialize($oSub));
	    $this->session->set_userdata('member_subscription_last_load',date('Y-m-d H:i:s'));
	    
	    $this->session->success('Subscription updated');
	    redirect('billing');
	}

	public function add_method() {
		$oMember = Service::load('member')->getMember(array('member_id'=>$this->_iMemberId))->first();
		if (empty($oMember)) {
			$this->session->error('Member lookup failed');
			redirect('billing');
			return false;
		}

		try {
			if (!$_POST['cc_num'] || !$_POST['exp_month'] || !$_POST['exp_year'] || !$_POST['cvv']) {
				$this->session->error('Please enter Card details. Error: STM');
				redirect('billing');
				return false;
			}
			if (!$_POST['first_name']) {
			    $this->session->error('Please enter First Name. Error: STM');
			    redirect('billing');
			    return false;
			}
			if (!$_POST['last_name']) {
			    $this->session->error('Please enter Last Name. Error: STM');
			    redirect('billing');
			    return false;
			}
			
			$card = [
			    'number' => $_POST['cc_num'],
			    'exp_month' => $_POST['exp_month'],
			    'exp_year' => $_POST['exp_year'],
			    'cvc' => $_POST['cvv'],
			];
			$billing_details = [
			    'address' => [
			        "city" => $_POST['city'],
			        "country" => $_POST['country'],
			        "line1" => $_POST['address1'],
			        "line2" => $_POST['address2'],
			        "postal_code" => $_POST['zip'],
			        "state" => $_POST['state']
			    ],
			    'name' => $_POST['first_name'] . ' ' . $_POST['last_name']
			];

			if (!$oMember->stripe_id) {
			    $oStripeCustomer = create_customer($this->session->userdata('member_email'));
                
				$oMember->stripe_id = $oStripeCustomer->id;
				Service::load('member')->updateMember($oMember);
			}
			
			$payment = create_payment($oMember->stripe_id, $card, $billing_details);
			if (!$payment) {
			    $this->session->error('Something went wrong. Please try again. Error: STM');
			    redirect('billing');
			    return false;
			}
			
			$this->_saveCreditCardFromStripeCard($payment);
			
		} catch (Exception $e) {
			$error = $e->getMessage();
			$this->session->error($error);
		}
		redirect('billing');
	}
	
	public function remove_payment_method($iBillingInfoId) {
	    $oBillingInfo = Service::load('billinginfo')->getBillingInfos(array('member_id'=>$this->_iMemberId,'billing_info_id'=>$iBillingInfoId))->reset();
	    if (empty($oBillingInfo)) {
	        $this->session->error('Card not found');
	        redirect('billing');
	    }
	    
	    remove_payment($oBillingInfo->stripe_id);
	    
	    Service::load('billingInfo')->deleteBillingInfo(array('member_id'=>$this->_iMemberId,'billing_info_id'=>$iBillingInfoId));
        
        $this->session->success('Card is now removed.');
        redirect('billing');
	}
	
	public function make_card_default($iBillingInfoId) {
	    $oBillingInfo = Service::load('billinginfo')->getBillingInfos(array('member_id'=>$this->_iMemberId,'billing_info_id'=>$iBillingInfoId))->reset();
	    if (empty($oBillingInfo)) {
	        $this->session->error('Card not found');
	        redirect('billing');
	    }
	    
	    $oMember = Service::load('member')->getMembers(array('member_id'=>$this->_iMemberId))->reset();
	    if (!$oMember->stripe_id) {
	        $this->session->error('Unable to set card as active (ERROR: MNSI)');
	    }
	    
	    $oCustomer = retrieve_customer($oMember->stripe_id);
	    $oCustomer->invoice_settings->default_payment_method = $oBillingInfo->stripe_id;
	    $oCustomer->save();
	    
	    $oBillingInfoActive = Service::load('billinginfo')->getBillingInfos(array(
	        'member_id'=>$this->_iMemberId
	        ,'status'=>BillingInfoModel::STATUS_ACTIVE)
        )->reset();
        if (!empty($oBillingInfoActive)) {
            $oBillingInfoActive->status = BillingInfoModel::STATUS_INACTIVE;
            Service::load('billingInfo')->updateBillingInfo($oBillingInfoActive);
        }
        
        $oBillingInfo->status = BillingInfoModel::STATUS_ACTIVE;
        Service::load('billingInfo')->updateBillingInfo($oBillingInfo);
        
        $this->session->success('Card is now active.');
        redirect('billing');
	}
	
	protected function _saveSubscriptionFromStripe($oSub,$iPlanId) {
	    $aPlans = ConfigService::getItem('plans');
	    $aPlan = $aPlans[$iPlanId];
	    
	    $iPrice = $aPlan['price'];
	    if (!empty($_POST['approvals'])) {
	        $iPrice += 250;
	    }
	    $oDBSubResponse = Service::load('subscription')->addSubscription(new SubscriptionModel(array(
	        'member_id'          => $this->_iMemberId
	        ,'create_date'       => date('Y-m-d H:i:s')
	        ,'plan_id'           => $iPlanId
	        ,'contract_limit'    => $aPlan['label']
	        ,'status'            => SubscriptionModel::StatusActive
	        ,'approvals'         => !empty($_POST['approvals']) ? SubscriptionModel::APPROVALS_ENABLED : SubscriptionModel::APPROVALS_DISABLED
	        ,'price'             => $iPrice
	        ,'start_date'        => date('Y-m-d H:i:s')
	        ,'last_checked'      => date('Y-m-d H:i:s')
	        ,'last_changed'      => date('Y-m-d H:i:s')
	        ,'next_billing_date' => date('Y-m-d H:i:s',strtotime('+1 month'))
	        ,'stripe_id'         => $oSub->id
	    )));
	    //log_message('required','oDBSubResponse: '.print_r($oDBSubResponse,true));
	    $oDBSub = $oDBSubResponse->reset();
	    
	    $this->session->set_userdata('member_current_subscription',serialize($oDBSub));
	    $this->session->set_userdata('member_subscription_last_load',date('Y-m-d H:i:s'));
	    
	    $this->session->success('Subscription Created');
	    return $oDBSub;
	}

	protected function _saveCreditCardFromStripeCard($payment) {
	    $billing_details = $payment->billing_details;
	    $address = $billing_details->address;
	    $oCard = $payment->card;
	    $name = explode(' ', $billing_details->name);
	    $first_name = '';
	    $last_name = '';
	    if (isset($name[0])) {
	        $first_name = $name[0];
	        unset($name[0]);
	        $last_name = implode(' ', $name);
	    }
	    
	    $oBIS = Service::load('billinginfo');
	    $oBIS->addBillingInfo(new BillingInfoModel(array(
	        'member_id'    => $this->_iMemberId
	        ,'first_name'  => $first_name
	        ,'last_name'   => $last_name
	        ,'cc_last_4'   => $oCard->last4
	        ,'cc_expire'   => str_pad($oCard->exp_month,2,'0',STR_PAD_LEFT).'/'.substr($oCard->exp_year, 2)
	        ,'cc_type'     => $this->_decodeStripeCardBrand($oCard->brand)
	        ,'address'     => $address->line1
	        ,'address2'    => $address->line2
	        ,'city'        => $address->city
	        ,'state'       => $address->state
	        ,'zip'         => $address->postal_code
	        ,'country'     => $address->country
	        ,'create_date' => date('Y-m-d H:i:s')
	        ,'stripe_id'   => $payment->id
	        ,'status'      => BillingInfoModel::STATUS_ACTIVE
	    )));
		
		send_analytic_event('Credit Card Added', null, null);
		
		return true;
	}

	protected function _decodeStripeCardBrand($sBrand) {
		$iCardType = BillingInfoModel::CCTypeUnknown;
		$sBrand = strtolower($sBrand);
		switch ($sBrand) {
			case 'visa':
				$iCardType = BillingInfoModel::CCTypeVisa;
				break;
			case 'mastercard':
				$iCardType = BillingInfoModel::CCTypeMastercard;
				break;
			case 'american express':
				$iCardType = BillingInfoModel::CCTypeAmericanExpress;
				break;
			case 'discover':
				$iCardType = BillingInfoModel::CCTypeDiscover;
				break;
		}
		return $iCardType;
	}
}