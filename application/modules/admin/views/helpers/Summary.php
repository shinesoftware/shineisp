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
		$this->view->income_yearly = Orders::incomeYearly($year);
		$this->view->income_quarter = Orders::incomeQuarter($year);
		$this->view->income_graph_monthly = Orders::incomeMonthly($year);
		$this->view->income_text_monthly = Orders::incomeMonthlyText($year);
				
		return $this->view->render ( 'partials/summary.phtml' );
	}

}