<?php
/**
 *
 * @version 0.1
 */
/**
 * Version helper
 * Print the migration version of ShineISP
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Version extends Zend_View_Helper_Abstract {
	
	public function Version() {
		$migration = new Doctrine_Migration(APPLICATION_PATH . '/configs/migrations');
		return $migration->getCurrentVersion();
	}
}
