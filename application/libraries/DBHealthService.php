<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DB Health Service
 *
 * @access public
 */
class DBHealthService extends HealthService
{
	/**
	 * CI Instance
	 *
	 * @access protected
	 */
	protected $_ci = null;

	/**
	 * Init Testing
	 *
	 * @access protected
	 * @return void
	 */
	protected function initTesting() {
		$this->_ci =& get_instance();
	}

	/**
	 * Test Connection
	 *
	 * @access protected
	 * @return array
	 */
	protected function _testConnection() {
		$bSuccess = 0;

		try {
			$aTables = $this->_ci->db->query('show tables')->result_array();
			if (!empty($aTables)) {
				$bSuccess = 1;
			}
		} catch (Exception $e) {
			log_message('error','DBHealthService::_testConnection exception: '.$e->getMessage());
		}

		return array(
			'success' => $bSuccess
		);
	}

/*
	protected function _testSubscriptionsOverdue() {
		$bSuccess = 1;

		try {
			$iCount = $this->_ci->db->where(
				'expire_date < "'.date('Y-m-d H:i:s',strtotime('-1 days')).'"'.
				' and status = 1 and type > 0'
			)->from('subscriptions')->count_all_results();

			if ($iCount) {
				return array('error'=>$iCount.' subs late to charge');
			}
		} catch (Exception $e) {
			log_message('error','DBHealthService::_testConnection exception: '.$e->getMessage());
		}

		return array(
			'success' => $bSuccess
		);
	}
*/
}
