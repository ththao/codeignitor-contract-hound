<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Encrypted File Model
 *
 * @access public
 */
class EncryptedFileModel extends BaseModel
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
		'file_hash'
		,'file_name'
		,'ivlen'
		,'iv'
		,'owner_id'
		,'parent_id'
		,'create_date'
		,'last_updated'
	);

	/**
	 * Defaults
	 *
	 * @access protected
	 */
	protected $aDefaults = array(
	);

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	protected function _generateFileHash() {
		if (empty($this->aData['file_hash'])) {
			$this->aData['file_hash'] = md5(
				'protectedkey867' . microtime()
				. $this->aData['owner_id']
				. $this->aData['parent_id']
				. $this->aData['create_date']
				. $this->aData['last_updated']
			);
		}

		return true;
	}

	public function regenerateFileHash() {
		$this->aData['file_hash'] = md5(
			'protectedkey867' . microtime()
			. $this->aData['owner_id']
			. $this->aData['parent_id']
			. $this->aData['create_date']
			. $this->aData['last_updated']
		);

		return true;
	}

	public function setFileName($sFileName) {
		if (empty($sFileName)) {
			return false;
		}

		$this->bIsSaved = false;
		$this->aData['file_name'] = $sFileName;
		return $this->_generateFileHash();
	}

	/**
	 * After populate update data
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _afterPopulate() {
		$this->_generateFileHash();
		return true;
	}
}
