<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Other Member Account Model
 *
 * @access public
 */
class OtherMemberAccountModel extends BaseModel
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
		'other_member_account_id'
		,'member_id'
		,'parent_id'
		,'create_date'
	);

	public function getName() {
		if (empty($this->aData['email'])) {
			return 'Unknown';
		}

		$sName = $this->aData['email'];
		if (empty($this->aData['first_name']) && empty($this->aData['last_name'])) {
			return $sName;
		}
		
		if (!empty($this->aData['first_name'])) {
			$sName = $this->aData['first_name'];
		}

		if (!empty($this->aData['first_name']) && !empty($this->aData['last_name'])) {
			$sName .= ' ';
		}

		if (!empty($this->aData['last_name'])) {
			$sName .= $this->aData['last_name'];
		}
		
		return $sName;
	}
}
