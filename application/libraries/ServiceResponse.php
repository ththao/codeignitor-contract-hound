<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Base Service Class
 *
 * @access public
 */
class ServiceResponse implements Iterator, Countable {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Statuses
	 *
	 * @access public
	 */
	const StatusGood          = 200;
	const StatusBadRequest    = 400;
	const StatusNotFound      = 404;
	const StatusError         = 500;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aMap = array(
		 self::StatusGood       => 'Good Request'
		,self::StatusBadRequest => 'Bad Request'
		,self::StatusNotFound   => 'Not Found'
		,self::StatusError      => 'Error'
	);

	/**
	 * Specialized Error Message
	 *
	 * @access protected
	 */
	protected $sError = '';

	/**
	 * Status
	 *
	 * @access protected
	 */
	protected $iStatus = 200;

	/**
	 * Count Of Results
	 *
	 * @access public
	 */
	public $count = 0;

	/**
	 * Count of all results
	 *
	 * @access public
	 */
	public $total = 0;

	/**
	 * Results Current Position
	 *
	 * @access protected
	 */
	protected $mPosition = null;

	/**
	 * Results
	 *
	 * @access protected
	 */
	protected $aResults = array();

	///////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 *
	 * @access public
	 * @param mixed $aData (Optional, array)
	 * @param integer $iStatus (Optional, StatusGood)
	 * @param string $sError (Optional)
	 */
	public function __construct($aData=array(),$iStatus=self::StatusGood,$sError='',$iTotalFound=0) {
		$this->sError = $sError;

		if (is_array($aData)) {
			$this->aResults = $aData;
			$this->iStatus = $iStatus;
			$this->count = count($aData);
		} elseif ($iStatus != self::StatusGood) {
			$this->iStatus = $iStatus;
		} else {
			$this->iStatus = self::StatusBadRequest;
		}

		if ($iTotalFound == 0) {
			$iTotalFound = $this->count;
		}
		$this->total = $iTotalFound;
	}

	/**
	 * Reset basics
	 *
	 * @access public
	 * @return boolean
	 */
	public function resetData() {
		$this->iStatus = self::StatusGood;
		$this->count = 0;
		$this->aResults = array();
		return true;
	}

	/**
	 * Get Results
	 *
	 * @access public
	 * @return array
	 */
	public function getResults() {
		return $this->aResults;
	}

	public function results() {
		return $this->aResults;
	}

	/**
	 * Set Results
	 *
	 * @access public
	 * @param array $aResults
	 * @return boolean
	 */
	public function setResults($aResults) {
		if (!is_array($aResults)) {
			return false;
		}

		$this->count = count($aResults);
		return $this->aResults = $aResults;
	}

	/**
	 * Get Status
	 *
	 * @access public
	 * @return integer
	 */
	public function getStatus() {
		return $this->iStatus;
	}

	/**
	 * Set Status
	 *
	 * @access public
	 * @param integer $iStatus
	 * @return boolean
	 */
	public function setStatus($iStatus) {
		if (empty($this->aMap[$iStatus])) {
			return false;
		}

		return $this->iStatus = $iStatus;
	}

	/**
	 * Is Ok
	 *
	 * @access public
	 * @return boolean
	 */
	public function isOk() {
		if (in_array($this->iStatus,array(self::StatusGood))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Has an error
	 *
	 * @access public
	 * @return boolean
	 */
	public function isError() {
		return !$this->isOk();
	}

	/**
	 * Get Error Message
	 *
	 * @access public
	 * @return string
	 */
	public function getError() {
		if (empty($this->sError)) {
			return $this->aMap[$this->iStatus];
		}

		return $this->sError;
	}

	/**
	 * Set Error Message
	 *
	 * @access public
	 * @param string $sError
	 * @return boolean
	 */
	public function setError($sError) {
		if (!is_string($sError)) {
			return false;
		}

		return $this->sError = $sError;
	}

	///////////////////////////////////////////////////////////////
	/////  Iterator Methods   ////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Count
	 *
	 * @access public
	 * @return boolean
	 */
	public function count() {
		//return count($this->aResults);
		return $this->count;
	}

	/**
	 * Size of data set
	 *
	 * @access public
	 * @return boolean
	 */
	public function size() {
		return sizeof($this->aResults);
	}

	/**
	 * First result without moving pointer
	 *
	 * @access public
	 * @return mixed
	 */
	public function first() {
		$aKeys = array_keys($this->aResults);
		if (count($aKeys)) {
			return $this->aResults[$aKeys[0]];
		} else {
			return null;
		}
	}

	/**
	 * Reset results
	 *
	 * @access public
	 * @return mixed
	 */
	public function reset() {
		$mReturn = reset($this->aResults);
		$this->mPosition = key($this->aResults);
		return $mReturn;
	}

	/**
	 * Reset results
	 *
	 * @access public
	 * @return mixed
	 */
	public function rewind() {
		return $this->reset();
	}

	/**
	 * Get previous element
	 *
	 * @access public
	 * @return mixed
	 */
	public function prev() {
		$mReturn = prev($this->aResults);
		$this->mPosition = key($this->aResults);
		return $mReturn;
	}

	/**
	 * Current element
	 *
	 * @access public
	 * @return mixed
	 */
	public function current() {
		return current($this->aResults);
	}

	/**
	 * Get key for current index
	 *
	 * @access public
	 * @return mixed
	 */
	public function key() {
		return key($this->aResults);
	}

	/**
	 * Next element
	 *
	 * @access public
	 * @return mixed
	 */
	public function next() {
		$mReturn = next($this->aResults);
		$this->mPosition = key($this->aResults);
		return $mReturn;
	}

	/**
	 * Get end element of results without moving pointer
	 *
	 * @access public
	 * @return mixed
	 */
	public function last() {
		$aKeys = array_keys($this->aResults);
		if (count($aKeys)) {
			return $this->aResults[end($aKeys)];
		} else {
			return null;
		}
	}

	/**
	 * Get end element of results
	 *
	 * @access public
	 * @return mixed
	 */
	public function end() {
		return end($this->aResults);
	}

	/**
	 * Position is set in results array
	 *
	 * @access public
	 * @return boolean
	 */
	public function valid() {
		return isset($this->aResults[$this->mPosition]);
	}

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
			$aData = &$this->aResults;
		}else {
			$aData = &$data;
		}

		foreach ($aData as $key=>$value) {
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
		if (is_null($data)) {
			return array('status'=>$this->iStatus,'data'=>$debug,'count'=>$this->count,'total'=>$this->total);
		} else {
			return $debug;
		}
	}
}
