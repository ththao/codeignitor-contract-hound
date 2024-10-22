<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
ini_set('display_errors', 1);

class Debug extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function index() {
		redirect(ConfigService::getItem('default_redirect_to'));
	}

	public function test_get_file() {
		echo "<pre>start\n\n";
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => 1157
		))->reset();

		if (empty($oContract)) {
			echo "Contract not found by id";
		} else {
			echo "Contract found by id\n";
		}

		$m = Service::load('contract')->retrieveFile($oContract);
		if (empty($m)) {
			echo "false";
		} else {
			var_dump($m);
		}
	}

	public function test_email() {
		$oMember = Service::load('member')->getMembers(array(
			'member_id' => 1
		))->reset();
		
		$oReminder = Service::load('reminder')->getReminders(array(
			'reminder_id' => 2
		))->reset();
		
		$oContract = Service::load('contract')->getContracts(array(
			'contract_id' => $oReminder->contract_id
		))->reset();

		$sSubject = ConfigService::getItem('app_name').' - Contract Reminder';
		
		$sMessageHTML = $this->load->view('emails/reminder_single',array(
			'oReminder'  => $oReminder
			,'oContract' => $oContract
			,'sEmail'    => $oMember->email
		),true);
		
		$sMessageText = "A reminder has been triggered for a contract you are associated to:\n\n{$oReminder->message}";

		$bSent = HelperService::sendEmail($oMember->email,ConfigService::getItem('support_email'),$sSubject,$sMessageText,$sMessageHTML);
		log_message('required','reminder email sent: '. $bSent?1:0);
		return $bSent;
	}

	public function test_add_member_to_contracts() {
		$this->load->model('contracts_m');
		$aResults = $this->contracts_m->directQuery(
			'select ctd.contract_id, cmtd.member_id from contracts ctd '.
				'left join contract_members cmtd on '.
					'ctd.contract_id = cmtd.contract_id '.
					'and cmtd.member_id = 206 '.
			'where ctd.parent_id = 199 and cmtd.member_id is null');
		echo "<pre>"; var_dump($aResults);

		foreach ($aResults as $aContract) {
			$oAdd = Service::load('contractmember')->addContractMember(new ContractMemberModel(array(
				'contract_id'  => $aContract['contract_id']
				,'member_id'   => 206
				,'level'       => ContractMemberModel::LEVEL_EDITOR
				,'create_date' => date('Y-m-d H:i:s')
			)));
			echo "Added to {$aContract['contract_id']}\n";
		}

		echo "Finished\n";
	}

	public function test_support_doc() {
		Service::load('contractsupportdoc')->addContractSupportDoc(new ContractSupportDocModel(array(
			'contract_id' => 10
			,'owner_id' => 1
			,'parent_id' => 1
			,'file_name' => 'test.txt'
			,'enct' => 1
			,'create_date' => date('Y-m-d H:i:s')
			,'last_updated' => date('Y-m-d H:i:s')
		)),UASDIR.'test.txt');
	}

	public function dump_session() {
		echo "<pre>pages:\n"; var_dump($this->_aPagesVisited);
		echo "\n\n site_url: ".site_url();
		echo "\n\n session:\n"; var_dump($_SESSION);
		echo "\n\n server:\n"; var_dump($_SERVER);
		echo "\n\n subscription\n"; var_dump($this->_getSubscription());
	}
}
