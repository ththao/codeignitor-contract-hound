<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Service Error Class
 *
 * @access public
 */
class ServiceError {

	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Statuses
	 *
	 * @access public
	 */
	const StatusBadRequest    = 400;
	const StatusNotFound      = 404;
	const StatusError         = 500;

	/**
	 * Status Translations
	 *
	 * @access protected
	 */
	protected $aMap = array(
		 self::StatusBadRequest => 'Bad Request'
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
	protected $iStatus = 400;

	/**
	 * Count Of Results
	 *
	 * @access public
	 */
	public $count = 0;

	/**
	 * Count of all results
	 */
	public $total_count = 0;

	///////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 *
	 * @access public
	 * @param integer $iStatus (Optional, StatusBadRequest)
	 * @param string $sError (Optional)
	 * @return void
	 */
	public function __construct($sError='',$iStatus=self::StatusBadRequest) {
		$this->sError = $sError;
		$this->iStatus = $iStatus;
	}

	/**
	 * Get Results
	 *
	 * @access public
	 * @return array
	 */
	public function getResults() {
		return array();
	}

	/**
	 * Get First Result
	 *
	 * @access public
	 * @return null
	 */
	public function first() {
		return null;
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
	 * Is Ok
	 *
	 * @access public
	 * @return boolean
	 */
	public function isOk() {
		return false;
	}

	/**
	 * Has an error
	 *
	 * @access public
	 * @return boolean
	 */
	public function isError() {
		return true;
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

	public function __toString() {
		return 'ServiceError: Code '.$this->iStatus.' Error '.$this->sError;
	}

	/**
	 * Present object data as string in debug mode
	 *
	 * @return string
	 */
	public function debug() {
		return array('error'=>$this->sError,'status'=>$this->iStatus);
	}
}