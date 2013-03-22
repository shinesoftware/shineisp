<?php
/**
 *
 * @author shine software
 * @version
 */
/**
 * Summary helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Summary extends Zend_View_Helper_Abstract{
	/**
	 *
	 */
	public function summary($year = null) {
		$this->view->income_quarter = Orders::incomeQuarter($year);
		$this->view->income_monthly = Orders::incomeMonthly($year);
		return $this->view->render ( 'partials/summary.phtml' );
	}

}
