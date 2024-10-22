<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Custom Field Model
 *
 * @access public
 */
class CustomFieldModel extends BaseModel
{
	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Fields for model
	 *
	 * @access protected
	 */
	protected $aFields = array(
		'custom_field_id'
		,'parent_id'
		,'label_text'
		,'default_value'
		,'description'
		,'required' // no = 0
		,'type'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'required' => 0
		,'type'    => self::TYPE_TEXT
	);

	/**
	 * Type Options
	 *
	 * @access public
	 */
	const TYPE_TEXT      = 0;
	const TYPE_MULTILINE = 1;
	const TYPE_CHECKBOX  = 2;

	protected $aTypes = array(
		self::TYPE_TEXT       => 'text'
		,self::TYPE_MULTILINE => 'multiline'
		,self::TYPE_CHECKBOX  => 'checkbox'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function setType($mValue) {
		// is number
		if (is_numeric($mValue) && isset($this->aTypes[$mValue])) {
			$this->aData['type'] = $mValue;
			return true;
			
		// is text
		} elseif (in_array($mValue,$this->aTypes)) {
			$this->aData['type'] = array_search($mValue, $this->aTypes);
			return true;
		}

		throw new Exception('Unknown Type');
	}

	/**
	 * Get Readable Statuses
	 *
	 * @access public
	 * @return string
	 */
	public function getReadableType() {
		return $this->_translateField('type',$this->aTypes);
	}
    
	public function getLabelField() {
	    return str_replace("\n",'<br/>', str_replace(' ', '_', strtolower($this->label_text)));
	}
}
