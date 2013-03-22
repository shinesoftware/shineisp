<?php
/**
 *
 * @version 0.1
 */
/**
 * Alerts helper
 * Create a simple list of alerts
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Alerts extends Zend_View_Helper_Abstract {
	/*
	 * 
	 */
	public function Alerts() {
		$registry = Zend_Registry::getInstance ();
		$translation = $registry->Zend_Translate;
		$alerts = array ();
		$this->view->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$this->view->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->view->action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (!empty($NS->customer)) {
			$data = $NS->customer;
			
			$orders = Orders::find_all_not_paid_ordersbyCustomerID ( $data ['customer_id'] );
			$tasks_errors = DomainsTasks::GetIncompleteTask ( $data ['customer_id'] );
			
			if (count ( $tasks_errors ) > 0) {
				foreach ( $tasks_errors as $task ) {
					if (! empty ( $task ['log'] )) {
						$alerts [] = array ('message' => $task ['domain'] . " - " . $translation->_ ( $task ['log'] ), $task ['domain_id'], 'link' => '/domains/edit/id/' . $task ['domain_id'], 'icon' => 'information' );
					}
				}
			}
			
			if (count ( $orders ) > 0) {
				foreach ( $orders as $order ) {
					if(!empty($order['invoice_id'])){
						$alerts [] = array ('message' => $translation->_ ( 'The invoice number %s of %s (%s euro) has been not payed yet, click here to show more details.', $order ['Invoices']['number'], Shineisp_Commons_Utilities::formatDateOut ( $order ['order_date'] ), $order ['grandtotal'] ), 'link' => '/orders/edit/id/' . $order ['order_id'], 'icon' => 'error' );	 	
					}else{
						$alerts [] = array ('message' => $translation->_ ( 'The order number %s that you have requested the %s with total %s euro has been not payed yet, click here to show more details.', $order ['order_id'], Shineisp_Commons_Utilities::formatDateOut ( $order ['order_date'] ), $order ['grandtotal'] ), 'link' => '/orders/edit/id/' . $order ['order_id'], 'icon' => 'attention' );
					}
				}
			}
			
			$this->view->alerts = $alerts;
			
			// Path of the template
			return $this->view->render ( 'partials/alerts.phtml' );
		}
	}
}
