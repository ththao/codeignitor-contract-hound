<?php
class GenericModel extends BaseModel {
	protected $sTable = '';
	protected $sLabel = '';

	public function initModel($aConfig) {
		// in case it is a label
		if (is_string($aConfig)) {
			ConfigService::loadFile('model');
			$aConfig = ConfigService::getItem($aConfig);
		}

		//////////////////////////////////////////////////
		// label
		if (!isset($aConfig['label'])) {
			throw new Exception('Label not found in config.');
		}

		$this->sLabel = $aConfig['label'];

		//////////////////////////////////////////////////
		// key
		if (!isset($aConfig['key'])) {
			throw new Exception('Key not found in config.');
		}

		$this->sPrimaryKey = $aConfig['key'];

		//////////////////////////////////////////////////
		// table
		if (!isset($aConfig['table'])) {
			throw new Exception('Table not found in config.');
		}

		$this->sTable = $aConfig['table'];

		//////////////////////////////////////////////////
		// properties
		if (empty($aConfig['properties'])) {
			throw new Exception('Properties not found in config.');
		}

		$this->aFields = $aConfig['properties'];

		//////////////////////////////////////////////////
		// properties
		if (!empty($aConfig['defaults'])) {
			$this->aDefaults = $aConfig['defaults'];
		}

		return true;
	}

	public function __construct($aValues,$aConfig) {
		$this->initModel($aConfig);
		parent::__construct($aValues);
	}

	public function getPrimaryKey() {
		return $this->sPrimaryKey;
	}

	public function getTable() {
		return $this->sTable;
	}

	public function getLabel() {
		return $this->sLabel;
	}
}
