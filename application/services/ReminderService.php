<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ReminderService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'ReminderModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add Reminder
	 *
	 * @access public
	 * @param ReminderModel $oReminder
	 * @return ServiceResponse
	 */
	public function addReminder(ReminderModel $oReminder) {
		$iResult = $this->_getModel('reminders_m')->addItem($oReminder->toArray());
		if ($iResult) {
			$oReminder->reminder_id = $iResult;
			$oReminder->isSaved(true);
			return new ServiceResponse(array($oReminder));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Delete Reminders
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function deleteReminders($aFilters=array()) {
		$bDelete = $this->_getModel('reminders_m')->deleteItems($aFilters);

		if ($bDelete) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get Reminder
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getReminder($aFilters=array()) {
		$aReminder = $this->_getModel('reminders_m')->getItem($aFilters);
		if (!empty($aReminder)) {
			return $this->_setupResponse(array($aReminder));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Reminders By Team Member Access
	 *
	 * @access public
	 * @param $iMemberId
	 * @param $active
	 * @param string $sOrderBy
	 * @param int $iLimit
	 * @param int $iOffset
	 * @return object|ServiceResponse
	 */
	public function getRemindersByTeamMember($iMemberId,$iParentId,$active,$sOrderBy='alert_date asc',$iLimit=7,$iOffset=0) {
		$aReminders = $this->_getModel('reminders_m')->getRemindersByTeamMember($iMemberId,$iParentId,$active,$sOrderBy='alert_date asc',$iLimit=5,$iOffset=0);
		if (!empty($aReminders)) {
			return $this->_setupResponse($aReminders);
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}

	/**
	 * Get Reminder Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getReminderCount($aFilters=array()) {
		$iCount = $this->_getModel('reminders_m')->countItems($aFilters);
		$oResponse = new ServiceResponse();
		$oResponse->total = $iCount;
		return $oResponse;
	}

	/**
	 * Get Reminders
	 *
	 * @access public
	 * @param array $aFilters
	 * @return ServiceResponse
	 */
	public function getReminders($aFilters=array(),$sSort='reminder_id asc',$iLimit=null) {
		$aReminders = $this->_getModel('reminders_m')->getItems($aFilters,$sSort,$iLimit);
		return $this->_setupResponse($aReminders);
	}

	/**
	 * Update Reminder
	 *
	 * @access public
	 * @param ReminderModel $oReminder
	 * @return ServiceResponse
	 */
	public function updateReminder(ReminderModel $oReminder) {
		if (!$oReminder->reminder_id) {
			return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
		}

		$bResponse = $this->_getModel('reminders_m')->updateItem($oReminder->toArray());
		if ($bResponse) {
			$oReminder->isSaved(true);
			return new ServiceResponse(array($oReminder));
		}

		return new ServiceResponse(false,ServiceResponse::StatusBadRequest);
	}
}
