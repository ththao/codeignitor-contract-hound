<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function index() {
		redirect(ConfigService::getItem('default_redirect_to'));
	}

	public function health() {
		$this->load->library('HealthService');
		$aResults = $this->healthservice->runAllTests();
		echo json_encode($aResults);
		return false;
	}

	public function send_reminders() {
		log_message('required','::send_reminders starting');
		do {
			$oReminders = Service::load('reminder')->getReminders(array(
				'status'          => ReminderModel::STATUS_ACTIVE
				,'alert_date <= ' => date('Y-m-d H:i:s')
				//,'contract_id'    => 11
			),'alert_date asc',10);
			log_message('required','::send_reminders count: '.count($oReminders));
			
			if (count($oReminders)) {
				$aContractIds = array();
				$aReminderIds = array();
				foreach ($oReminders as $oReminder) {
					$aContractIds[$oReminder->contract_id] = $oReminder->contract_id;
					$aReminderIds[$oReminder->reminder_id] = $oReminder->reminder_id;
				}
				
				$aContracts = array();
				$oContracts = Service::load('contract')->getContracts(array(
					'contract_id' => $aContractIds
				));
				foreach ($oContracts as $oContract) {
					$aContracts[$oContract->contract_id] = $oContract;
				}
				unset($oContracts);
				
				//ReminderMemberModel
				$aSortedMembers = array();
				$oReminderMembers = Service::load('remindermember')->getReminderMembers(array(
					'reminder_id' => $aReminderIds
				));
				foreach ($oReminderMembers as $oReminderMember) {
					$aSortedMembers[$oReminderMember->reminder_id][$oReminderMember->member_id] = $oReminderMember->member_id;
				}
				unset($oReminderMembers);
				
				foreach ($oReminders as $oReminder) {
					if (!empty($aSortedMembers[$oReminder->reminder_id])) {
						$oMembers = Service::load('member')->getMembers(array(
							'member_id' => $aSortedMembers[$oReminder->reminder_id]
						));
						
						foreach ($oMembers as $oMember) {
							$this->_sendReminderEmail($oReminder,$aContracts[$oReminder->contract_id],$oMember);
						}
					}
					
					$oReminder->status = ReminderModel::STATUS_COMPLETED;
					Service::load('reminder')->updateReminder($oReminder);
				}
			}
			
		} while ($oReminders->count);
		
		ConfigService::loadFile('healthchecks');
		$hc_checks = ConfigService::getItem('hc_checks');
		file_get_contents($hc_checks['send_reminders']);
		
	}

	protected function _sendReminderEmail($oReminder,$oContract,$oMember) {
		$sSubject = ConfigService::getItem('app_name').' - Contract Reminder';
		
		$sUrl = site_url().'contracts/view/'.$oContract->contract_id;

		$sMessageHTML = $this->load->view('emails/reminder_single',array(
			'oReminder'  => $oReminder
			,'oContract' => $oContract
			,'sEmail'    => $oMember->email
			,'sUrl'      => $sUrl
		),true);
		
		$sMessageText = "Below is a reminder that you or a team member scheduled for the following contract: ".
			"{$oContract->name}\n\n{$oReminder->message}\n\nView Contract: {$sUrl}";

		$bSent = HelperService::sendEmail($oMember->email,'reminders@contracthound.com',$sSubject,$sMessageText,$sMessageHTML);
		log_message('required','reminder email sent: rid > '.$oReminder->reminder_id.' mid > '.$oMember->member_id.' : '. ($bSent?'success':'failed'));
		return $bSent;
	}

	public function expire_trials() {
		log_message('required','expiring old trials');
		$oSS = Service::load('subscription');
		do {
			$oSubs = $oSS->getSubscriptions(array(
				'status'            => SubscriptionModel::StatusTrial
				,'expire_date <= ' => date('Y-m-d H:i:s')
			),'expire_date asc',25);
			
			foreach ($oSubs as $oSub) {
				$oSub->status = SubscriptionModel::StatusExpired;
				$oSub->approvals = SubscriptionModel::APPROVALS_DISABLED;
				$oSS->updateSubscription($oSub);
			}
		} while($oSubs->count);
		
		ConfigService::loadFile('healthchecks');
		$hc_checks = ConfigService::getItem('hc_checks');
		file_get_contents($hc_checks['expire_trials']);
	}

	public function check_subscriptions() {
	    ConfigService::loadFile('billing');
	    $this->load->helper('stripe');
		
		$oSS = Service::load('subscription');
		$oSLS = Service::load('subscriptionlog');

		do {
			$oSubs = $oSS->getSubscriptions(array(
				'status'            => SubscriptionModel::StatusActive
				,'last_checked <= ' => date('Y-m-d H:i:s',strtotime('-1 month'))
				,'stripe_id is not null'
			),'last_checked asc',25);
			
			foreach ($oSubs as $oSub) {
				$oSub->last_checked = date('Y-m-d H:i:s');

				try {
					$oStripeSub = retrieve_subscription($oSub->stripe_id);
				} catch (Exception $e) {
					$oSLS->addLog(new SubscriptionLogModel(array(
						'member_id'        => $oSub->member_id
						,'subscription_id' => $oSub->subscription_id
						,'stripe_id'       => $oSub->stripe_id
						,'status'          => 'invalid'
						,'message'         => '' // translate later
						,'create_date'     => date('Y-m-d H:i:s')
						,'amount'          => $oSub->price
					)));

					$oSub->status = SubscriptionModel::StatusCancelled;
					$oSub->expire_date = date('Y-m-d H:i:s');
					$oSub->cancel_date = date('Y-m-d H:i:s');
					$oSS->updateSubscription($oSub);
					continue;
				}

				$oSLS->addLog(new SubscriptionLogModel(array(
					'member_id'        => $oSub->member_id
					,'subscription_id' => $oSub->subscription_id
					,'stripe_id'       => $oSub->stripe_id
					,'status'          => $oStripeSub->status
					,'message'         => '' // translate later
					,'create_date'     => date('Y-m-d H:i:s')
					,'amount'          => $oSub->price
				)));

				if (strcmp($oStripeSub->status,'active') !== 0) {
					$oSub->status = SubscriptionModel::StatusCancelled;
					$oSub->expire_date = date('Y-m-d H:i:s');
					if ($oStripeSub->ended_at) {
						$oSub->expire_date = date('Y-m-d H:i:s',strtotime($oStripeSub->ended_at));
					} else {
						$oSub->next_billing_date = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($oSub->next_billing_date)));
					}
				}

				//echo '<pre>'; var_dump($oStripeSub,$oSub); return true;
				$oSS->updateSubscription($oSub);
			}
		} while($oSubs->count);
		
		ConfigService::loadFile('healthchecks');
		$hc_checks = ConfigService::getItem('hc_checks');
		file_get_contents($hc_checks['check_subscriptions']);
	}

	public function update_intercom() {
		log_message('required','cron::update_intercom start');
		$this->load->library('Intercom');
		$oMembers = Service::load('member')->getMembers();

		$iMemberCount = count($oMembers);
		foreach($oMembers as $i=>$oMember) {

			//Custom attributes returns null if no subscription exists
			// Therefore the user has not logged in and is still a lead
			if($custom_attributes = $this->_getCustomAttributes($oMember)) {

				//Check if user is lead
				//	if lead then convert user
				$leads = $this->intercom->getLeads(['email' => $oMember->email]);
				if($leads->total_count > 0) {
					foreach($leads->contacts as $lead)
					{
						$this->intercom->convertLead($lead->user_id, $oMember->email);
					}
				}

				//Update user
				$this->intercom->updateUser(
						$oMember->member_id,                                //id
						$oMember->email,                                    //email
						trim("$oMember->first_name $oMember->last_name"),	//name
						$custom_attributes									//customData
				);

				log_message('required',"cron::update_intercom ran {$i}/{$iMemberCount} => {$oMember->member_id} {$oMember->email} ".print_r($custom_attributes,true));
				usleep(.5 * 1000000);;
			}
		}
		log_message('required','cron::update_intercom finished');
		
		ConfigService::loadFile('healthchecks');
		$hc_checks = ConfigService::getItem('hc_checks');
		file_get_contents($hc_checks['update_intercom']);
	}

	public function purge_deleted_contracts() {
		//TODO actually build this
	}

	/**
	 * Get relevant member array
	 *
	 * @param MemberModel $oMember
	 * @return array
	 * @throws Exception
	 */
	protected function _getCustomAttributes(MemberModel $oMember)
	{
		$custom_attributes = new stdClass();
		$custom_attributes->account_type = $oMember->parent_id == $oMember->member_id ? 'Parent' : 'Sub';

		$oContractsCount = Service::load('contract')->getContractCount(array(
			'parent_id' => $oMember->member_id,
			'status'  => array(ContractModel::STATUS_ACTIVE,ContractModel::STATUS_EXPIRED)
		));
		$custom_attributes->contracts_added = $oContractsCount->total;

		// figure out current subscription
		$oSubscription = $this->_getSubscriptionByMemberId($oMember->member_id);
		if (!empty($oSubscription)) {
			$custom_attributes->plan = $oSubscription->plan_id;
			$custom_attributes->rate = $oSubscription->price;

			switch ($oSubscription->status) {
				case SubscriptionModel::StatusActive:
					$custom_attributes->status = 'Customer';
					break;
				case SubscriptionModel::StatusTrial:
					$custom_attributes->status = (!$oSubscription->isExpired() ? 'Trial' : 'Expired Trial');
					break;
				case SubscriptionModel::StatusExpired:
					$custom_attributes->status = 'Expired';
					break;
				case SubscriptionModel::StatusFree:
					$custom_attributes->status = 'Free';
					break;
				case SubscriptionModel::StatusCancelled:
				case SubscriptionModel::StatusTerminated:
				case SubscriptionModel::StatusSuspended:
					$custom_attributes->status = 'Cancelled';
					break;
			}

			return $custom_attributes;
		}

		return null;
	}
}
