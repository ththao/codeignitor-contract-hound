<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contract Log Model
 *
 * User Log MI:X T:0 S:0
 * Updated Contract MI:0 T:2 S:0
 *
 * @access public
 */
class ContractLogModel extends BaseModel
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
		'contract_log_id'
		,'contract_id'
		,'member_id'
		,'message'
		,'type'
		,'create_date'
	);

	const TYPE_NOTE             = 0; // user added notes
	const TYPE_ALERT            = 1; // user added alerts
	const TYPE_UPDATE           = 2; // contract update
	const TYPE_APPROVED         = 3; // contract approved
	const TYPE_REJECTED         = 4; // contract approved
	const TYPE_FULLY_APPROVED   = 5; // contract fully approved
	const TYPE_SIGNER_APPROVED  = 6; // signer appoved
	const TYPE_SIGNER_REJECTED  = 7; // signer rejected
	const TYPE_DOCUSIGN_GENERIC = 8; // push, pull, whatever

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Get Readable Statuses
	 *
	 * @access public
	 * @return string
	 */
	public function getReadableStatus() {
		return $this->_getReadable('status',$this->aStatuses);
	}

}
