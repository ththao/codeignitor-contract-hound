<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Import extends User_Controller {

	protected $_aContractReservedFields = array(
		'id'
		,'name'
		,'start'
		,'end'
		,'value'
	);

	protected $_aCustomFields = array();

	protected $_aBoards = array();

	///////////////////////////////////////////////////////////////////////////
	///  Super Methods   /////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function __construct() {
		parent::__construct();

		if (!$this->bCurrentlyLoggedInMemberIsAdmin) {
			redirect('welcome');
		}

		$this->_aCustomFields = Service::load('customfield')->getCustomFields(array('parent_id'=>$this->_iParentId));

		$oBoards = Service::load('board')->getBoards(array('parent_id'=>$this->_iParentId));
		foreach ($oBoards as $oBoard) {
			$this->_aBoards[$oBoard->board_id] = trim(strtolower($oBoard->name));
		}
	}

	///////////////////////////////////////////////////////////////////////////
	///  Methods   ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Main view contracts
	 *
	 * @access public
	 */
	public function index() {
		if (!can_access_feature('new_upload_version',$this->_iMemberId)) {
			redirect('contracts/upload');
		}

		if ($this->_isPost()) {
			//echo '<pre>'; var_dump($_FILES);

			if (!empty($_FILES['update_file'])) {
				$aCsv = array_map('str_getcsv', file($_FILES['update_file']['tmp_name']));
				$aData = array();
			    array_walk($aCsv, function(&$aData) use ($aCsv) {
			      $aData = array_combine($aCsv[0], $aData);
			    });
			    array_shift($aCsv); # remove column header
			    var_dump($aCsv);

				$aErrors = array();
				$iErrorCount = 0;
				foreach ($aCsv as $iRowId=>$aFileUpdate) {
					$aRowErrors = $this->_validateRow($aFileUpdate);
					if (!empty($aRowErrors)) {
						$iErrorCount++;
						$aErrors[$iRowId] = $aRowErrors;
					}
					if ($iErrorCount > 100) {
						break;
					}
				}

				if ($iErrorCount) {
					var_dump($aErrors);
				} else {
					foreach ($aCsv as $aRow) {
						$this->_updateContract($aRow);
					}
				}
			}

			//return true;
		}

		$this->build('import/index');
	}

	protected function _updateContract($aRow) {
		if (empty($aRow['id'])) {
			return false;
		}

		$oContract = Service::load('contract')->getContract(array(
			'contract_id' => $aRow['id']
			,'parent_id'  => $this->_iParentId
		))->first();

		foreach (array(
			'name'   => 'name'
			,'start' => 'start_date'
			,'end'   => 'end_date'
			,'value' => 'valued'
		) as $sRowKey => $sContractKey)
		{
			if (isset($aRow[$sRowKey]) && $aRow[$sRowKey] != '') {
				if($sContractKey == 'start_date' || $sContractKey == 'end_date'){
					$oContract->setField($sContractKey, convert_utc_datetime($aRow[$sRowKey],$this->cTimeZone));
				}else {
					$oContract->setField($sContractKey, $aRow[$sRowKey]);
				}
				//var_dump($sContractKey,$aRow[$sRowKey]);
			} else {
				$oContract->setField($sContractKey,null);
			}
		}

 		//return true;
		Service::load('contract')->updateContract($oContract);
		
		foreach ($this->_aCustomFields as $oCustomField) {
			
		}
	}

	protected function _validateRow($aRow) {
		$aErrors = array();
		if (!isset($aRow['id']) || !preg_match('/^\d+$/', $aRow['id'])) {
			$aErrors['id'] = 'Invalid contract ID';
		}

		if (empty($aRow['name'])) {
			$aErrors['id'] = 'A name is required for the contract.';
		}

		if (!empty($aRow['start']) && !$this->_isValidDate($aRow['start'])) {
			$aErrors['start'] = 'Invalid start date.';
		}

		if (!empty($aRow['end']) && !$this->_isValidDate($aRow['end'])) {
			$aErrors['end'] = 'Invalid end date.';
		}

		if (isset($aRow['value']) && $aRow['value'] != '' && !$this->_isValidValued($aRow['value'])) {
			$aErrors['value'] = 'Invalid value.';
		}

		$aCustomFieldErrors = $this->_validateCustomFields($aRow);

		return $aErrors;
	}

	protected function _isValidValued($mValued) {
		if (!preg_match('/^\d+(\.\d{2}){0,1}$/',$mValued)) {
			echo "{$mValued} failed regex\n";
			return false;
		}

		if ($mValued < 0) {
			echo "{$mValued} too low\n";
			return false;
		}

		if ($mValued > 1000000000) {
			echo "{$mValued} too high\n";
			return false;
		}

		return true;
	}

	protected function _validateCustomFields($aRow) {
		$aErrors = array();

		foreach ($aRow as $mIndex=>$mValue) {
			if (in_array($mIndex,$this->_aContractReservedFields)) {
				continue;
			}

			foreach ($this->_aCustomFields as $oCustomField) {
				if (strcmp($mIndex,$oCustomField->label_text)===0) {
					if (strlen($mValue) > 2000) {
						$aErrors[retud($mIndex)] = 'Custom field values must be less than 2000 characters';
					}
				}
			}
		}

		return $aErrors;
	}

	protected function _isValidDate($sDate) {
		// validate format
		if (!preg_match('/^\d{1,2}-\d{1,2}-(2\d){0,1}\d{2}$/',$sDate)) {
			return false;
		}

		$aDate = explode('-',$sDate);

		// validate month
		if ($aDate[0] < 1 || $aDate[0] > 12) {
			return false;
		}

		// validate day
		if ($aDate[1] < 1 || $aDate[1] > 31) {
			return false;
		}

		// validate year
		if ($aDate[2] < 2000 || $aDate[2] > 4000) {
			return false;
		}

		return true;
	}
}
