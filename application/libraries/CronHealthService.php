<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cron Health Service
 *
 * @access public
 */
class CronHealthService extends HealthService
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
}
