<?php

/**
 * Summary helper
 *
 * @uses viewHelper Zend_View_Helper
 */

class Admin_View_Helper_Summary extends Zend_View_Helper_Abstract{
	
	public function summary($year = null) {
		
		$this->view->income_yearly = Orders::incomes();
		$this->view->income_quarter = Orders::incomes(array(), 'quarter');
		$this->view->income_text_monthly = Orders::incomes(array(), 'month');
		$this->view->income_graph_monthly = Orders::incomes(array(), 'month');
		return $this->view->render ( 'partials/summary.phtml' );
	}

}