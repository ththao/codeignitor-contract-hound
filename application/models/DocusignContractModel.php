<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Docusign Access Token Model
 *
 * @access public
 */
class DocusignContractModel extends BaseModel
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
		'docusign_contract_id'
		,'contract_id'
		,'docusign_envelope_id'
		,'status'
		,'create_date'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
		'status' => self::STATUS_PENDING
	);

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const STATUS_PENDING          = 0;
	const STATUS_SENT_TO_DOCUSIGN = 1;
	const STATUS_SENT_TO_SIGNERS  = 2;
	const STATUS_COMPLETED        = 3;
	const STATUS_REJECTED         = 4;
	const STATUS_CANCELED         = 5;
	const STATUS_CHC_MISSING      = 6;  // Missing doc in CH

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function getActive() {
		if (in_array($this->aData['status'],array(
			self::STATUS_PENDING
			,self::STATUS_SENT_TO_DOCUSIGN
			,self::STATUS_SENT_TO_SIGNERS
		))) {
			return true;
		}
		
		return false;
	}
}
