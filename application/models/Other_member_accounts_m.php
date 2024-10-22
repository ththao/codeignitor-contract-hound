<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Other_member_accounts_m extends MY_Model {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_table = 'other_member_accounts';

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $primary_key = 'other_member_account_id';

	public function getOtherMemberAccountsWithMemberAccountData($aFilters) {
		$sDB = $this->sDB;
		$this->$sDB->select('other_member_accounts.*, members.company as company_name, members.first_name, members.last_name, members.email, members.avatar');
		$this->$sDB->join('members','members.member_id = other_member_accounts.member_id');
		return $this->getItems($aFilters);
	}

	public function getOtherMemberAccountsWithParentAccountData($aFilters) {
		$sDB = $this->sDB;
		$this->$sDB->select('other_member_accounts.*, members.company as parent_company_name, members.first_name as parent_first_name, members.last_name as parent_last_name, members.email as parent_email');
		$this->$sDB->join('members','members.member_id = other_member_accounts.parent_id');
		return $this->getItems($aFilters);
	}
}
