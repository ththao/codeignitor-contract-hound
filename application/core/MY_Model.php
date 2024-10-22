<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ClearStats MY_Model Extension
 *
 * @access public
 */
class MY_Model extends CI_Model
{
	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The database table to use, only
	 * set if you want to bypass the magic
	 *
	 * @var string
	 */
	protected $_table;

	/**
	 * The primary key, by default set to
	 * `id`, for use in some functions.
	 *
	 * @var string
	 */
	protected $primary_key = 'id';

	/**
	 * Which DB table lives
	 *
	 * @access protected
	 */
	protected $sDB = 'db';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Apply Filters
	 *
	 * @access protected
	 * @param array $aFilters
	 * @return boolean
	 */
	protected function applyFilters($aFilters=array()) {
		if (empty($aFilters) || !is_array($aFilters)) {
			return true;
		}
		$sDB = $this->sDB;

		foreach ($aFilters as $sField=>$mValue) {
			if (is_int($sField) && is_string($mValue)) {
				$this->$sDB->where($mValue);
			} elseif (is_int($sField) && is_array($mValue)) {
				$sMethod = $mValue['method'];
				if (!empty($mValue['extra'])) {
					$this->$sDB->$sMethod($mValue['field'],$mValue['value'],$mValue['extra']);
				} else {
					$this->$sDB->$sMethod($mValue['field'],$mValue['value']);
				}
			} elseif (strpos($sField,' !=') && is_array($mValue)) {
				$sField = str_replace(' !=','',$sField);
				$this->$sDB->where_not_in($sField,$mValue);
			} elseif (is_array($mValue)) {
				$this->$sDB->where_in($sField,$mValue);
			} else {
				$this->$sDB->where($sField,$mValue);
			}
		}

		return true;
	}

	public function directQuery($sQuery,$aParams=array()) {
		$sDB = $this->sDB;
		return $this->$sDB->query($sQuery,$aParams)->result_array();
	}

	/**
	 * Add Item
	 *
	 * @access public
	 * @param array $aItem
	 * @return integer
	 */
	public function addItem($aItem,$bOnDuplicate=false) {
		if (isset($aItem[$this->primary_key])) {
			return false;
		}
		$sDB = $this->sDB;

		$bResult = $this->$sDB->insert($this->_table,$aItem,NULL,$bOnDuplicate);
		if ($bResult) {
			return $this->$sDB->insert_id();
		} else {
			return false;
		}
	}

	/**
	 * Count Items
	 *
	 * @access public
	 * @param array $aFilters
	 * @return integer
	 */
	public function countItems($aFilters=array()) {
		$sDB = $this->sDB;

		$this->applyFilters($aFilters);
		return $this->$sDB->count_all_results($this->_table);
	}

	/**
	 * Delete Items
	 *
	 * @access public
	 * @param array $aFilters
	 * @return boolean
	 */
	public function deleteItems($aFilters=array()) {
		if (empty($aFilters)) {
			return false;
		}
		$sDB = $this->sDB;

		$this->applyFilters($aFilters);
		return $this->$sDB->delete($this->_table);
	}

	/**
	 * Get Count
	 *
	 * @access public
	 * @param array $aFilters
	 * @return integer
	 */
	public function getCount($aFilters=array()) {
		$sDB = $this->sDB;
		$this->applyFilters($aFilters);
		return $this->$sDB->count_all_results($this->_table);
	}

	/**
	 * Get One Items
	 *
	 * @access public
	 * @param array $aFilters
	 * @return array
	 */
	public function getItem($aFilters=array()) {
		$sDB = $this->sDB;

		$this->applyFilters($aFilters);
		$this->$sDB->limit(1);
		return $this->$sDB->get($this->_table)->row_array();
	}

	/**
	 * Get Many Items
	 *
	 * @access public
	 * @param array $aFilters (Optional)
	 * @param string|array $mOrderBy (Optional)
	 * @param integer $iLimit (Optional)
	 * @param integer $iOffset (Optional)
	 * @return array
	 */
	public function getItems($aFilters=array(),$mOrderBy='',$iLimit=0,$iOffset=0,$aFields=array()) {
		$sDB = $this->sDB;

		if (!empty($aFields)) {
			$this->$sDB->select(implode(', ',$aFields));
		}

		if (!empty($iLimit) && !empty($iOffset)) {
			$this->$sDB->limit($iLimit,$iOffset);
		} elseif (!empty($iLimit)) {
			$this->$sDB->limit($iLimit);
		}

		if (!empty($mOrderBy)) {
			if (is_array($mOrderBy)) {
				foreach ($mOrderBy as $aSort) {
					$this->$sDB->order_by($aSort[0],$aSort[1]);
				}
			} else {
				$this->$sDB->order_by($mOrderBy);
			}
		} else {
			$this->$sDB->order_by($this->primary_key,'asc');
		}

		$this->applyFilters($aFilters);
		return $this->$sDB->get($this->_table)->result_array();
	}

	/**
	 * Get Max for Field for Table
	 *
	 * @access public
	 * @param string $sField
	 * @param array $aFilters
	 * @return mixed
	 */
	public function getMax($sField,$aFilters=array()) {
		if (empty($sField) || !is_string($sField)) {
			return false;
		}

		$sDB = $this->sDB;
		$this->applyFilters($aFilters);
		$this->$sDB->select_max($sField);
		$aResult = $this->$sDB->get($this->_table)->row_array();

		$mMax = false;
		if (!empty($aResult) && is_array($aResult)) {
			$mMax = reset($aResult);
		}
		return $mMax;
	}

	/**
	 * Update Fields
	 *
	 * @access public
	 * @param array $aFilters
	 * @param array $aNewSettings
	 * @return boolean
	 */
	public function updateFields($aFilters,$aNewSettings) {
		if (!is_array($aFilters) || !is_array($aNewSettings) ||
			empty($aFilters) || empty($aNewSettings))
		{
			return false;
		}
		$sDB = $this->sDB;

		$this->applyFilters($aFilters);
		$bResult = $this->$sDB->update($this->_table,$aNewSettings);
		return $bResult;
	}

	/**
	 * Update Item
	 *
	 * @access public
	 * @param array $aItem
	 * @return boolean
	 */
	public function updateItem($aItem) {
		$sDB = $this->sDB;

		$this->$sDB->where($this->primary_key,$aItem[$this->primary_key]);
		return $this->$sDB->update($this->_table,$aItem);
	}

	/**
	 * Update Items Batch
	 *
	 * @access public
	 * @param array $aFilters
	 * @param array $aNewSettings
	 * @return boolean
	 */
	public function updateItemsBatch($aFilters,$aNewSettings) {
		if (!is_array($aFilters) || !is_array($aNewSettings) ||
			empty($aFilters) || empty($aNewSettings))
		{
			return false;
		}
		$sDB = $this->sDB;

		$this->applyFilters($aFilters);
		$bResult = $this->$sDB->update($this->_table,$aNewSettings);
		return $bResult;
	}
}
