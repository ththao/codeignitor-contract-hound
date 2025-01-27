<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Integrations extends User_Controller {

	///////////////////////////////////////////////////////////////////////////
	///  Properties   ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////


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

	/**
	 *
	 * @access public
	 */
	public function index() {
		$oToken = Service::load('docusignaccesstoken')->getDocusignAccessToken(array(
			'parent_id' => $this->_iParentId
			,'status'   => DocusignAccessTokenModel::STATUS_ACTIVE
		))->reset();

		$this->set('oToken',$oToken);
		$this->set('sHeader','Integrations');
		$this->build('integrations/docusign');
	}

	/**
	 *
	 * @access public
	 */
	public function docusign() {
		$this->set('sHeader','DocuSign Settings');
		$this->build('integrations/docusign');
	}
}
