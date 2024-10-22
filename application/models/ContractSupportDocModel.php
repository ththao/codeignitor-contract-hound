<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contract Support Doc Model
 *
 * @access public
 */
class ContractSupportDocModel extends EncryptedFileModel
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
		'contract_support_doc_id'
		,'contract_id'
		,'owner_id'
		,'parent_id'
		,'file_name'
		,'file_hash'
		,'ivlen'
		,'iv'
		,'enct'
		,'create_date'
		,'last_updated'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'enct' => 1 // orig encoding
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function getKeyId() {
		return $this->contract_support_doc_id;
	}
}
