<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ReminderMemberService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ReminderMemberModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add ReminderMember
	 *
	 * @access public
	 * @param ReminderMemberModel $oReminderMember
	 * @return ServiceResponse
	 */
	public function addReminderMember(ReminderMemberModel $oReminderMember) {
		$iResult = $this->_getModel('reminder_members_m')->addItem($oReminderMember->toArray());
		if ($iResult) {
			$oReminderMember->reminder_member_id = $iResult;
			$oReminderMember->isSaved(true);
			return new ServiceResponse(array($oReminderMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete ReminderMembers
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteReminderMembers($aFilters=array()) {
		$bDelete = $this->_getModel('reminder_members_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Delete ReminderMembers by Contract Parent Id
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteReminderMembersByParentId($iParentId) {
		$bDelete = $this->_getModel('reminder_members_m')->directQuery(
			'delete from reminder_members r_m where r_m.reminder_id in '.
				'(select reminder_id from reminders re '.
					'left join contracts contr on re.contract_id = contr.contract_id where contr.parent_id = ?)',array($iParentId));

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get ReminderMember
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getReminderMember($aFilters=array()) {
		$aReminderMembers = $this->_getModel('reminder_members_m')->getItem($aFilters);
		if (!empty($aReminderMembers)) {
			return $this->_setupResponse(array($aReminderMembers));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get ReminderMember Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getReminderMemberCount($aFilters=array()) {
		$iCount = $this->_getModel('reminder_members_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get ReminderMembers
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getReminderMembers($aFilters=array(),$sSort='reminder_member_id asc',$iLimit=null) {
		$aReminders = $this->_getModel('reminder_members_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aReminders);
	}

	/**
	 * Update ReminderMember
	 *
	 * @access public
	 * @param ReminderMemberModel $oReminderMember
	 * @return ServiceResponse
	 */
	public function updateReminderMember(ReminderMemberModel $oReminderMember) {
		if (!$oReminderMember->reminder_member_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('reminder_members_m')->updateItem($oReminderMember->toArray());
		if ($bResponse) {
			$oReminderMember->isSaved(true);
			return new ServiceResponse(array($oReminderMember));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
