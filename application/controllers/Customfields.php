<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Customfields extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Custom Field Validation
	 *
	 * @access protected
	 */
	protected $custom_field_validation = array(
		array(
			'field' => 'label',
			'label' => 'Label',
			'rules' => 'trim|no_html|max_length[35]|required|callback__check_not_reserved'
		),
		/*array(
			'field' => 'default',
			'label' => 'Default',
			'rules' => 'trim|no_html|empty_to_null'
		),*/
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim|no_html|max_length[100]|empty_to_null'
		),
		array(
			'field' => 'type',
			'label' => 'Type',
			'rules' => 'trim|callback__is_valid_type'
		),
		array(
			'field' => 'required',
			'label' => 'Required',
			'rules' => 'trim|required|callback__is_valid_required'
		)
	);

	///////////////////////////////////////////////////////////////////////////
	///  Super Methods   /////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function __construct() {
		parent::__construct();

		$this->load->library('form_validation');
	}

	///////////////////////////////////////////////////////////////////////////
	///  Methods   ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function _check_not_reserved($mValue) {
		$mValue = trim(strtolower($mValue));
		if (in_array($mValue,array(
			'id'
			,'name'
			,'start'
			,'end'
			,'value'
		))) {
			$this->form_validation->set_message('_check_not_reserved', 'You can not use a reserved label.');
			return false;
		}

		return true;
	}

	public function _is_valid_type($mValue) {
		if (!in_array($mValue,array('text','multiline','checkbox'))) {
			$this->form_validation->set_message('_is_valid_type', 'Invalid value for type.');
			return false;
		}

		return true;
	}

	public function _is_valid_required($mValue) {
		if (!in_array($mValue,array('true','false'))) {
			$this->form_validation->set_message('_is_valid_required', 'Invalid value for Required.');
			return false;
		}

		return true;
	}

	/**
	 *
	 * @access public
	 */
	public function index() {
		$oCustomFields = Service::load('customfield')->getCustomFields(array('parent_id'=>$this->_iParentId));

		$this->set('oCustomFields',$oCustomFields);
		$this->set('sHeader','Custom Fields');
		$this->build('customfields/index');
	}

	public function add_field() {
		//log_message('required','post: '.print_r($_POST,true));

		if (!$this->_isPost()) {
			echo json_encode(array('success'=>0,'error'=>'Invalid Request'));
			return true;
		}

		$oCFS = Service::load('customfield');
		if (!empty($_POST['id'])) {
			if (!is_numeric($_POST['id'])) {
				echo json_encode(array('success'=>0,'error'=>'Custom field not found.'));
				return true;
			}

			$oCustomField = $oCFS->getCustomFields(array('parent_id'=>$this->_iParentId,'custom_field_id'=>$_POST['id']))->reset();
			if (empty($oCustomField)) {
				echo json_encode(array('success'=>0,'error'=>'Custom field not found.'));
				return true;
			}
		} else {
			$oCustomField = new CustomFieldModel(array(
				'parent_id' => $this->_iParentId
			));
		}

		//log_message('required',$this->_iMemberId.' contract::edit $_POST '.print_r($_POST,true));
		$this->form_validation->set_rules($this->custom_field_validation);
		if ($this->form_validation->run()) {
			$oCustomField->required = (set_value('required') === 'true')?true:false;

			try {
				$oCustomField->type = set_value('type');
			} catch (Exception $e) {
				echo json_encode(array('success'=>0,'error'=>'Unable to save. '));
				return true;
			}

			$oCustomField->description = set_value('description');

			//$oCustomField->default_value = set_value('default');
			$oCustomField->label_text = set_value('label');

			if ($oCustomField->custom_field_id) {
				$oResponse = $oCFS->updateCustomField($oCustomField);
			} else {
				$oResponse = $oCFS->addCustomField($oCustomField);
			}

			if ($oResponse->isOk()) {
				$iId = $oResponse->reset()->custom_field_id;
				echo json_encode(array('success'=>1,'message'=>'Custom Field Updated','id'=>$iId));
				return true;
			} else {
				echo json_encode(array('success'=>0,'error'=>'Unable to save.'));
				return true;
			}
		} else {
			echo json_encode(array('success'=>0,'error'=>'Unable to save. '.$this->form_validation->first_error()));
			return true;
		}

		echo json_encode(array('success'=>0,'error'=>'Unknown Error'));
	}

	/**
	 *
	 * @access public
	 */
	public function remove_field() {
		//log_message('required','post: '.print_r($_POST,true));

		if (!$this->_isPost()) {
			echo json_encode(array('success'=>0,'error'=>'Invalid Request'));
			return true;
		}

		$oCFS = Service::load('customfield');
		if (empty($_POST['id'])) {
			echo json_encode(array('success'=>0,'error'=>'Custom field not found.'));
			return true;
		}

		if (!is_numeric($_POST['id'])) {
			echo json_encode(array('success'=>0,'error'=>'Custom field not found.'));
			return true;
		}

		$oCustomField = $oCFS->getCustomFields(array('parent_id'=>$this->_iParentId,'custom_field_id'=>$_POST['id']))->reset();
		if (empty($oCustomField)) {
			echo json_encode(array('success'=>0,'error'=>'Custom field not found.'));
			return true;
		}

		$oResponse = $oCFS->deleteCustomFields(array('parent_id'=>$this->_iParentId,'custom_field_id'=>$_POST['id']));
		if ($oResponse->isOk()) {
			if ($oCustomField->type == CustomFieldModel::TYPE_CHECKBOX) {
				Service::load('customfieldvaluecheckbox')->deleteCustomFieldValueCheckboxes(array('parent_id'=>$this->_iParentId,'custom_field_id'=>$_POST['id']));
			} else {
				Service::load('customfieldvaluetext')->deleteCustomFieldValueTexts(array('parent_id'=>$this->_iParentId,'custom_field_id'=>$_POST['id']));
			}
			echo json_encode(array('success'=>1,'message'=>'Custom Field removed.'));
			return true;
		} else {
			echo json_encode(array('success'=>0,'error'=>'Unable to remove custom field.'));
			return true;
		}


		echo json_encode(array('success'=>0,'error'=>'Unknown Error'));
	}
}