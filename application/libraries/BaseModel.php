<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Base Model
 *
 * @access public
 */
class BaseModel {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Primary Key
	 *
	 * @access protected
	 */
	protected $sPrimaryKey = '';

	/**
	 * Stored Values
	 *
	 * @access protected
	 */
	protected $aData = array();

	/**
	 * Actual stored fields
	 *   as in in db
	 *
	 * @access protected
	 */
	protected $aFields = array();

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array();

	/**
	 * Saved
	 *
	 * @access protected
	 */
	protected $bIsSaved = false;

	///////////////////////////////////////////////////////////////
	/////  Super Methods   ///////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 *
	 * @access public
	 * @param array $aParams
	 */
	public function __construct($aParams=array()) {
		$this->reset();

		if (!empty($aParams)) {
			$this->populateData($aParams);
		}
	}

	/**
	 * get value
	 *
	 * @access public
	 * @param string $sField
	 * @return mixed
	 */
	public function __get($sField) {
		$sField = strtolower($sField);

		$method = 'get'.$this->_convertFieldName($sField);
		if (method_exists($this, $method)) {
			return $this->$method();
		}

		if (!isset($this->aData[$sField])) {
			return null;
		}

		return $this->aData[$sField];
	}

	/**
	 * set value
	 *
	 * @access public
	 * @param string $sField
	 * @param mixed $mValue
	 * @return boolean
	 */
	public function __set($sField,$mValue) {
		$sField = strtolower($sField);

		$method = 'set'.$this->_convertFieldName($sField);
		if (method_exists($this, $method)) {
			return $this->$method($mValue);
		}

		$this->_checkIsSaved($sField,$mValue);

		return $this->aData[$sField] = $mValue;
	}

	public function getLabel() {
		return get_class($this);
	}

	public function defaultValue($sField) {
		if (!in_array($sField,$this->aFields)) {
			return null;
		}
		
		if (isset($this->aDefaults[$sField])) {
			return $this->aDefaults[$sField];
		}
		
		return null;
	}

	/**
	 * Set Field
	 *
	 * @access public
	 * @param string $sField
	 * @param mixed $mValue (Optional, NULL)
	 * @return boolean
	 */
	public function setField($sField,$mValue=NULL) {
		$sField = strtolower($sField);

		return $this->$sField = $mValue;
	}

	/**
	 * Convert Field Name from this_that to ThisThat
	 *
	 * @access protected
	 * @param string $sFieldName
	 * @return string
	 */
	protected function _convertFieldName($sFieldName) {
		return str_replace(' ','',ucwords(str_replace('_',' ',$sFieldName)));
	}

	/**
	 * Get Readable Translation
	 *
	 * @access protected
	 * @param string $sField
	 * @param array $aDataSet
	 * @param string $sDefault (Optional, 'Unknown')
	 * @return mixed
	 */
	protected function _getReadable($sField,$aDataSet,$sDefault='Unknown') {
		if (empty($sField) || empty($aDataSet) || !is_array($aDataSet) || !isset($this->aData[$sField])) {
			return $sDefault;
		}

		$iValue = $this->aData[$sField];
		if (!isset($aDataSet[$iValue])) {
			return $sDefault;
		}

		return $aDataSet[$iValue];
	}

	/**
	 * Get Readable Translation
	 *
	 * @access protected
	 * @param string $sField
	 * @param array $aDataSet
	 * @param string $sDefault (Optional, 'Unknown')
	 * @return mixed
	 */
	protected function _translateField($sField,$aDataSet,$sDefault='Unknown') {
		return $this->_getReadable($sField,$aDataSet,$sDefault);
	}

	/**
	 * Check Is Saved
	 *
	 * @access protected
	 * @param string $sField
	 * @param mixed $mValue
	 * @return boolean
	 */
	protected function _checkIsSaved($sField,$mValue) {
		if (in_array($sField,$this->aFields) &&
			((isset($this->aData[$sField]) && $this->aData[$sField] !== $mValue)) || !isset($this->aData[$sField]))
		{
			$this->bIsSaved = false;
		}
		return true;
	}

	///////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////
	/////////////////////////////////////////////////////////////

	public function __toString() {
		return print_r($this->debug(),true);
	}

	/**
	 * Present object data as string in debug mode
	 *
	 * @param mixed $data
	 * @param array $objects
	 * @return string
	 */
	public function debug($data=null, &$objects=array()) {
		$debug = array();

		if (is_null($data)) {
			$hash = spl_object_hash($this);
			if (!empty($objects[$hash])) {
				return '*** RECURSION ***';
			}
			$objects[$hash] = true;
			$data = &$this->aData;
		}

		foreach ($data as $key=>$value) {
			if (is_null($value)) {
				$debug[$key] = 'NULL';
			} elseif (is_scalar($value)) {
				$debug[$key] = $value;
			} elseif (is_array($value)) {
				$debug[$key] = $this->debug($value, $objects);
			} elseif ($value instanceof BaseModel) {
				$debug[$key.' ('.get_class($value).')'] = $value->debug(null, $objects);
			}
		}

		ksort($debug);
		return $debug;
	}

	/**
	 * Get Difference between current values
	 *   and submitted
	 *
	 * @access public
	 * @param array $aOtherValues
	 * @return array
	 */
	public function getDifference($aOtherValues) {
		if (!is_array($aOtherValues) && in_array('toArray',get_class_methods($aOtherValues))) {
			$aOtherValues = $aOtherValues->toArray();
		}

		if (!is_array($aOtherValues)) {
			return false;
		}

		$aCurrentData = $this->toArray();
		return array_diff($aCurrentData,$aOtherValues);
	}

	/**
	 * Get Difference Count
	 *
	 * @access public
	 *
	 * @param array $aOtherValues
	 * @return integer
	 */
	public function getDifferenceCount($aOtherValues) {
		$aDiff = $this->getDifference($aOtherValues);
		return count($aDiff);
	}

	/**
	 * Remove field
	 *
	 * @access public
	 * @param string $sField
	 * @return boolean
	 */
	public function removeField($sField) {
		$sField = strtolower($sField);

		if (in_array($sField,$this->aFields)) {
			$this->bIsSaved = false;
		}

		unset($this->aData[$sField]);
		return true;
	}

	/**
	 * Set Value
	 *
	 * @access public
	 * @param $sField
	 * @param $mValue
	 * @return boolean
	 */
	public function setValue($sField,$mValue) {
		$sField = strtolower($sField);

		$method = 'set'.$this->_convertFieldName($sField);
		if (method_exists($this, $method)) {
			return $this->$method($mValue);
		}

		$this->_checkIsSaved($sField,$mValue);

		return $this->aData[$sField] = $mValue;
	}

	/**
	 * Is Saved
	 *
	 * @access public
	 * @param boolean $bNewValue (Optional)
	 * @return boolean
	 */
	public function isSaved($bNewValue=null) {
		if ($bNewValue === null) {
			return $this->bIsSaved;
		}

		return $this->bIsSaved = $bNewValue;
	}

	/**
	 * Get Fields
	 *
	 * @access public
	 * @return array
	 */
	public function getFields() {
		return $this->aFields;
	}

	/**
	 * Get Field Value
	 *
	 * @access public
	 * @param string $sField
	 * @return mixed
	 */
	public function fieldValue($sField) {
		$sField = strtolower($sField);

		if (isset($this->aData[$sField])) {
			return $this->aData[$sField];
		}

		return null;
	}

	/**
	 * Is Field
	 *
	 * @access public
	 * @param string $sField
	 * @return boolean
	 */
	public function isField($sField) {
		$sField = strtolower($sField);

		if (in_array($sField,$this->aFields)) {
			return true;
		}

		return false;
	}

	/**
	 * Has a field set
	 *
	 * @access public
	 * @param string $sField
	 * @return boolean
	 */
	public function hasField($sField) {
		if (!is_string($sField)) {
			return false;
		}

		$sField = strtolower($sField);
		if (isset($this->aData[$sField])) {
			return true;
		}

		return false;
	}

	/**
	 * Is Field Set
	 *
	 * @access public
	 * @param string $sField
	 * @return boolean
	 */
	public function isFieldSet($sField) {
		$sField = strtolower($sField);

		if (isset($this->aData[$sField])) {
			return true;
		}

		return false;
	}

	/**
	 * Convert static values to saveable array
	 *
	 * @access public
	 * @return array
	 */
	public function toArray() {
		$aValues = array();

		foreach ($this->aFields as $sField) {
			if (isset($this->aData[$sField])) {
				$aValues[$sField] = $this->aData[$sField];
			} else {
				$aValues[$sField] = null;
			}
		}

		return $aValues;
	}

	/**
	 * Get all current data
	 *   also get default fields as null
	 *
	 * @access public
	 * @return array
	 */
	public function allToArray() {
		$aReturn = $this->toArray();

		if (!empty($this->aData)) {
			foreach ($this->aData as $sField=>$mValue) {
				if (!in_array($sField, $this->aFields)) {
					$aReturn[$sField] = $this->aData[$sField];
				}
			}
		}

		return $aReturn;
	}

	/**
	 * Purge extra fields
	 *
	 * @access public
	 * @return boolean
	 */
	public function purgeExtra() {
		if (empty($this->aData)) {
			return true;
		}

		foreach ($this->aData as $sField=>$mValue) {
			if (!in_array($sField, $this->aFields)) {
				unset($this->aData[$sField]);
			}
		}

		return true;
	}

	/**
	 * Before populate update data
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _beforePopulate() {
		return true;
	}

	/**
	 * Populate Data Points
	 *
	 * @access public
	 * @param array|string $aParams
	 * @param mixed $mValue (Optional if first param is array)
	 * @return boolean
	 */
	public function populateData($aParams,$mValue=null) {
		$this->_beforePopulate();

		if (is_array($aParams)) {
			foreach ($aParams as $sField=>$mValue) {
				$sField = strtolower($sField);
				$this->_checkIsSaved($sField,$mValue);
				$this->aData[$sField] = $mValue;
			}
		} elseif (is_string($aParams)) {
			$sField = strtolower($aParams);
			$this->_checkIsSaved($sField,$mValue);
			$this->aData[$sField] = $mValue;
		}

		$this->_afterPopulate();

		return true;
	}

	/**
	 * After populate update data
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _afterPopulate() {
		return true;
	}

	/**
	 * Set Primary Key
	 *
	 * @access public
	 * @param mixed $mValue
	 * @return boolean
	 */
	public function setPrimaryKey($mValue) {
		if (!empty($this->sPrimaryKey)) {
			$this->_checkIsSaved($this->sPrimaryKey,$mValue);
			return $this->aData[$this->sPrimaryKey] = $mValue;
		}

		return false;
	}

	/**
	 * Reset Fields
	 *
	 * @access public
	 * @return boolean
	 */
	public function reset() {
		$this->bIsSaved = false;
		return $this->aData = $this->aDefaults;
	}
}
