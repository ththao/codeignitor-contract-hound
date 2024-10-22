<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contract Revision Model
 *
 * @access public
 */
class ContractRevisionModel extends ContractModel
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
		'contract_revision_id'
		,'contract_id'
		,'owner_id'
		,'parent_id'
		,'board_id'
		,'name'
		,'company'
		,'start_date'
		,'end_date'
		,'valued'
		,'type'
		,'status'
		,'enct'
		,'file_name'
		,'file_hash'
		,'ivlen'
		,'iv'
		,'create_date'
		,'last_updated'
		,'revision_date'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
}
