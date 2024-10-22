<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class GenericService extends Service {
	public function init(GenericModel $oModel) {
		$this->_getModel($oModel->table)->init($oModel);
		$this->sModelClass = $oModel->label;
		return $this;
	}

	public function addItem(GenericModel $oModel) {
		$iId = $this->_getModel($oModel->table)->addItem($oModel->toArray());

		if ($iId) {
			$sPrimary = $oModel->primary_key;
			$oModel->$sPrimary = $iId;
			$oModel->isSaved(true);
			return new ServiceResponse(array($oModel));
		}

		return $this->_setupErrorResponse();
	}

	public function deleteItems($aFilters) {
		return $this->_getModel($this->sModelClass)->deleteItems($aFilters);
	}

	public function getItems($oModel,$aFilters=array(),$mOrderBy='',$iLimit=0,$iOffset=0) {
		$aItems = $this->_getModel($oModel->table)->getItems($aFilters,$mOrderBy,$iLimit,$iOffset);
		return $this->_setupResponse($aItems);
	}

	public function updateItem(GenericModel $oModel) {
		$bUpdated = $this->_getModel($oModel->table)->updateItem($oModel->toArray());

		if ($bUpdated) {
			$oModel->isSaved(true);
			return new ServiceResponse(array($oModel));
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get Model Instance
	 *
	 * @throws Exception
	 * @access protected
	 * @param string $sClassName
	 *   ex: module/modelName
	 * @return mixed
	 */
	protected function _getModel($sClassName) {
		if (file_exists('application/models/'.ucfirst($sClassName).'.php')) {
			return parent::_getModel($sClassName);
		}

		if (file_exists('application/models/'.ucfirst($sClassName).'_m.php')) {
			return parent::_getModel($sClassName.'_m');
		}

		return parent::_getModel('generic_m');
	}
}
