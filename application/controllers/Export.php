<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Export extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Super Methods   /////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function __construct() {
		parent::__construct();
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
		$this->build('export/index');
	}

	public function export() {
		$oCustomFields = Service::load('customfield')->getCustomFields(array('parent_id'=>$this->_iParentId));
		$contractIds = (isset($_GET['ids']) && $_GET['ids']) ? explode(',', base64_decode($_GET['ids'])) : '';
		if ($contractIds) {
            sort($contractIds);
		}
		
		$sDate = date('YmdHis');
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=\"contracts_{$sDate}.csv\"");
		echo "\"id\",\"name\",\"vendor\",\"type\",\"start\",\"end\",\"owner\",\"value\",\"active reminders\",\"expired reminders\"";

		$aSortedCustomFieldIds = array();
		foreach ($oCustomFields as $oCustomField) {
			$aSortedCustomFieldIds[] = $oCustomField->custom_field_id;
			echo ',"'.str_replace('"','""',$oCustomField->label_text).'"';
		}
		echo "\n";

		$iOffset = 0;
		$iLimit = 50;
		do {
		    $aFilters = array(
		        'contracts.parent_id' => $this->_iParentId
		        ,'contracts.status'   => ContractModel::STATUS_ACTIVE
		    );
		    if ($contractIds) {
		        if (count($contractIds) > $iOffset) {
		            if (count($contractIds) > $iOffset + $iLimit) {
		                $aFilters['contracts.contract_id IN (' . implode(',', array_slice($contractIds, $iOffset, $iLimit)) . ')'] = null;
		            } else {
		                $aFilters['contracts.contract_id IN (' . implode(',', array_slice($contractIds, $iOffset)) . ')'] = null;
		            }
		        } else {
		            $aFilters['contracts.contract_id IN (0)'] = null;
		        }
		        
		        $oContracts = Service::load('contract')->exportContracts($aFilters,'contracts.contract_id asc',null,0);
		    } else {
		        $oContracts = Service::load('contract')->exportContracts($aFilters,'contracts.contract_id asc',$iLimit,$iOffset);
		    }
			//log_message('required',__METHOD__.' found: '.$oContracts->count.' l: '.$iLimit.' o: '.$iOffset);
			$iOffset += $iLimit;

			if ($oContracts->count) {
				$aContractIds = array();
				foreach ($oContracts as $oContract) {
					$aContractIds[] = $oContract->contract_id;
				}

				$oCustomFieldValueTexts = Service::load('customfieldvaluetext')->getCustomFieldValueTexts(array(
					'parent_id'    => $this->_iParentId
					,'contract_id' => $aContractIds
				));
				$aCustomFieldsSortedByContract = array();
				foreach ($oCustomFieldValueTexts as $oCustomFieldValueText) {
					if (!isset($aCustomFieldsSortedByContract[$oCustomFieldValueText->contract_id])) {
						$aCustomFieldsSortedByContract[$oCustomFieldValueText->contract_id] = array();
					}

					$aCustomFieldsSortedByContract[$oCustomFieldValueText->contract_id][] = $oCustomFieldValueText;
				}

				foreach ($oContracts as $oContract) {
					//log_message('required',__METHOD__.' exporting: '.$oContract->contract_id);
					// id
					echo "\"{$oContract->contract_id}\"";

					// name
					echo ',"'.str_replace('"','""',$oContract->name).'"';
					
					// vendor
					echo ',"'.str_replace('"','""',$oContract->company).'"';
					
					// document type
					$type = $oContract->type ? tl('Buy-side') : tl('Sell-side');
					echo ',"'.$type.'"';
					
					// start_date
					if ($oContract->start_date) {
						echo ',"'.convertto_local_datetime($oContract->start_date,$this->cTimeZone,'%x').'"';/*date('m-d-Y',strtotime($oContract->start_date))*/
					} else {
						echo ',""';
					}

					// end_date
					if ($oContract->end_date) {
						echo ',"'.convertto_local_datetime($oContract->end_date,$this->cTimeZone,'%x').'"'; /*date('m-d-Y',strtotime($oContract->end_date))*/
					} else {
						echo ',""';
					}
					
					// owner
					$sOwnerName = trim($oContract->first_name.' '.$oContract->last_name);
					if (empty($sOwnerName)) {
					    $sOwnerName = $oContract->email;
					}
					echo ',"'.str_replace('"','""',$sOwnerName).'"';
					
					// valued
					echo ',"'.str_replace('"','""',$oContract->valued).'"';
					
					// active reminders
					echo ',"'.number_format($oContract->active_r).'"';
					
					// expired reminders
					echo ',"'.number_format($oContract->expired_r).'"';
					
					if (isset($aCustomFieldsSortedByContract[$oContract->contract_id])) {
						foreach ($aSortedCustomFieldIds as $iCustomFieldId) {
							$bFound = false;
							foreach ($aCustomFieldsSortedByContract[$oContract->contract_id] as $oCustomFieldValueText) {
								if ($oCustomFieldValueText->custom_field_id == $iCustomFieldId) {
									$bFound = true;
									echo ',"'.str_replace('"','""',$oCustomFieldValueText->field_value).'"';
								}
							}
							if (!$bFound) {
								echo ',""';
							}
						}
					} else {
						foreach ($aSortedCustomFieldIds as $iCustomFieldId) {
							echo ',""';
						}
					}
					echo "\n";
					//log_message('required',__METHOD__.' exported: '.$oContract->contract_id);
				}
			}
		} while ($oContracts->count > 0);
	}
}
