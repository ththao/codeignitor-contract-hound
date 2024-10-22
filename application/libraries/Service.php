<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Base Service Class
 *
 * @access public
 */
class Service
{
	///////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Instance of Service Class
	 *
	 * @static
	 * @access protected
	 */
	protected static $_aInstances = array();

	/**
	 * Instance of CI Model
	 *
	 * @static
	 * @access protected
	 */
	protected static $_aModels = array();

	/**
	 * Instance of CI Libraries
	 *
	 * @static
	 * @access protected
	 */
	protected static $_aLibraries = array();

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'BaseModel';

	/**
	 * Found Services
	 *
	 * @static
	 * @access protected
	 */
	protected static $aFoundServices = array();

	/**
	 * Allowed Characters
	 *
	 * @access public
	 */
	const AllowedCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	///////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////
	/////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		$this->_loadModelClass($this->sModelClass);
	}

	/**
	 * Load Model Class
	 *
	 * @throws Exception
	 * @access protected
	 * @param $sClass
	 * @return bool
	 */
	protected function _loadModelClass($sClass) {
		if (class_exists($sClass)) {
			return true;
		}

		if (!class_exists($sClass) && file_exists('application/models/'.$sClass.'.php')) {
			return require_once('application/models/'.$sClass.'.php');
		}

		throw new Exception('Model Class not found: '.$sClass);
	}

	/**
	 * Scan for all services
	 *
	 * @static
	 * @access protected
	 * @return boolean
	 */
	protected static function _findServices() {
		$aServices = glob('application/services/*Service.php');

		foreach ($aServices as $sFullPath) {
			$sService = strtolower(str_replace('.php','',basename($sFullPath)));
			self::$aFoundServices[$sService] = $sFullPath;
		}

		return true;
	}

	/**
	 * Load service
	 *
	 * @static
	 * @throws Exception
	 * @access public
	 * @param string $sServiceName
	 * @return object
	 */
	public static function load($sServiceName) {
		$sServiceName = strtolower($sServiceName);
		if (strpos($sServiceName,'service')===false) {
			$sServiceName .= 'service';
		}

		if (empty(self::$aFoundServices)) {
			self::_findServices();
		}

		if (empty(self::$aFoundServices[$sServiceName])) {
			throw new Exception("Requested Service Not found: ".$sServiceName, 1);
			return false;
		}

		$sFullPath = self::$aFoundServices[$sServiceName];
		$sClassName = basename($sFullPath,'.php');
		require_once($sFullPath);

		return $sClassName::getInstance();
	}

	/**
	 * Get Instance of Class
	 *
	 * @static
	 * @access public
	 * @return object
	 */
	public static function getInstance() {
		$sClassName = get_called_class();

		if (empty(self::$_aInstances[$sClassName])) {
			self::$_aInstances[$sClassName] = new $sClassName();
		}

		return self::$_aInstances[$sClassName];
	}

	/**
	 * Set Instance for Mocking
	 *
	 * @static
	 * @access public
	 * @param object $oInstance
	 * @param string|bool $sClassName (Default false)
	 * @return boolean
	 */
	public static function setInstance($oInstance,$sClassName=false) {
		if (empty($sClassName)) {
			$sClassName = get_class($oInstance);
		}

		return self::$_aInstances[$sClassName] = $oInstance;
	}

	/**
	 * Get Service Instance
	 *
	 * @static
	 * @access public
	 * @param string
	 * @return mixed
	 */
	public static function instance($sClassName) {
		$_ci = &get_instance();

		$sBaseClassName = $sClassName;
		if (strpos($sClassName,'/')) {
			$sBaseClassName = substr($sClassName,strpos($sClassName,'/')+1);
		}

		return $sBaseClassName::getInstance();
	}

	/**
	 * Get Library Instance
	 *
	 * @throws Exception
	 * @access protected
	 * @param string $sClassName
	 *   ex: module/library
	 * @return mixed
	 */
	protected function _getLibrary($sClassName) {
		$sBaseClassName = $sClassName;
		if (strpos($sClassName,'/') !== false) {
			$sBaseClassName = substr($sClassName,strpos($sClassName,'/')+1);
		}

		if (!empty(self::$_aLibraries[$sBaseClassName])) {
			return self::$_aLibraries[$sBaseClassName];
		}

		get_instance()->load->library($sClassName);
		$sLoadedName = strtolower($sClassName);
		$oLibrary = get_instance()->$sLoadedName;

		if (empty($oLibrary)) {
			throw new Exception("Requested Library Not Loaded: ".$sClassName, 1);
		}

		self::$_aLibraries[$sBaseClassName] = $oLibrary;
		return self::$_aLibraries[$sBaseClassName];
	}

	/**
	 * Set Library Instance
	 *
	 * @access public
	 * @param string $sBaseClassName
	 * @param object $oInstance
	 * @return boolean
	 */
	public function setLibraryInstance($sBaseClassName,$oInstance) {
		return self::$_aLibraries[$sBaseClassName] = $oInstance;
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
		$sBaseClassName = $sClassName;
		if (strpos($sClassName,'/') !== false) {
			$sBaseClassName = substr($sClassName,strpos($sClassName,'/')+1);
		}

		if (!empty(self::$_aModels[$sBaseClassName])) {
			return self::$_aModels[$sBaseClassName];
		}

		$oModel = get_instance()->load->model($sClassName);

		if (empty($oModel)) {
			throw new Exception("Requested Model Not Loaded: ".$sClassName, 1);
		}

		self::$_aModels[$sBaseClassName] = $oModel;
		return self::$_aModels[$sBaseClassName];
	}

	/**
	 * Set Model Instance
	 *
	 * @access public
	 * @param string $sBaseClassName
	 * @param object $oInstance
	 * @return boolean
	 */
	public function setModelInstance($sBaseClassName,$oInstance) {
		return self::$_aModels[$sBaseClassName] = $oInstance;
	}

	/**
	 * Fixes up values to be final
	 *
	 * @access public
	 * @param array $aDataSet
	 * @return array
	 */
	protected function _finalizeDataSet($aDataSet,$sModelClass=false) {
		return $aDataSet;
	}

	/**
	 * Populate Model Class
	 *
	 * @throws Exception
	 * @access protected
	 * @param array $aResults
	 * @param string|bool $sModelClass (Optional)
	 * @return array
	 */
	protected function _populateResults($aResults,$sModelClass=false) {
		$aReturn = array();

		if (empty($aResults) || !is_array($aResults)) {
			return array();
		}

		if (empty($sModelClass)) {
			$sModelClass = $this->sModelClass;
		}

		if (!class_exists($sModelClass) && file_exists('application/models/'.$sModelClass)) {
			require_once('application/models/'.$sModelClass);
		}

		if (!class_exists($sModelClass)) {
			throw new Exception('model does not exist: '.$sModelClass);
		}

		foreach ($aResults as $aResult) {
			$aResult = $this->_finalizeDataSet($aResult,$sModelClass);
			$oNewModelObject = new $sModelClass($aResult);
			$oNewModelObject->isSaved(true);
			$aReturn[] = $oNewModelObject;
		}

		return $aReturn;
	}

	/**
	 * Setup Error Response
	 *
	 * @access protected
	 * @param integer $iStatus (Optional, Default ServiceResponse::StatusBadRequest)
	 * @param string $sError (Optional)
	 * @return ServiceResponse
	 */
	protected function _setupErrorResponse($iStatus=ServiceResponse::StatusBadRequest,$sError='') {
		return new ServiceResponse(false,$iStatus,$sError);
	}

	/**
	 * Setup Service Error Response
	 *
	 * @access protected
	 * @param integer $iStatus (Optional, Default ServiceResponse::StatusBadRequest)
	 * @param string $sError (Optional)
	 * @return ServiceError
	 */
	protected function _errorResponse($iStatus=ServiceResponse::StatusBadRequest,$sError='') {
		return new ServiceError($iStatus,$sError);
	}

	/**
	 * Setup Response
	 *
	 * @access protected
	 * @param array $aResults
	 * @param string|bool $sModelClass (Optional)
	 * @param integer $iTotalFound (Optional, Default 0)
	 * @return object ServiceResponse
	 */
	protected function _setupResponse($aResults,$sModelClass=false,$iTotalFound=0) {
		$aResults = $this->_populateResults($aResults,$sModelClass);
		return new ServiceResponse($aResults,ServiceResponse::StatusGood,'',$iTotalFound);
	}

	/**
	 * Get Shortened Url From Id
	 *
	 * @access protected
	 * @param integer $integer
	 * @param $base (Optional, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	 * @return string
	 */
	protected function _getShortenedURLFromID($integer,$base=self::AllowedCharacters) {
		$length = strlen($base);
		$out = '';
		while($integer > $length - 1)
		{
			$out = $base[fmod($integer, $length)] . $out;
			$integer = floor( $integer / $length );
		}
		return $base[$integer] . $out;
	}

	/**
	 * Get Id from Url Slug
	 *
	 * @access protected
	 * @param string $string
	 * @param string $base (Optional)
	 * @return integer
	 */
	protected function _getIDFromShortenedURL($string,$base=self::AllowedCharacters) {
		$length = strlen($base);
		$size = strlen($string) - 1;
		$string = str_split($string);
		$out = strpos($base, array_pop($string));
		foreach($string as $i => $char) {
			$out += strpos($base, $char) * pow($length, $size - $i);
		}
		return $out;
	}
}
