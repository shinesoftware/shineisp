<?php
/**
 *
 * @version 
 */
/**
 * Dashboard helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Dashboard extends Zend_View_Helper_Abstract {
	
	public function dashboard() {
		return $this;
	}
	
	/**
	 * DomainsExpiration
	 * List of all the domains near the expiring date
	 * @return array
	 */
	public function DomainsExpiration() {
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$data = $ns->customer;
			return array ('records' => Domains::getExpiringDomains($data ['customer_id']), 'actions' => array ('/domains/edit/id/' => 'Show' ), 'pager' => true );
		} else {
			return null;
		}
	}
	
	/**
	 * ServicesExpiration
	 * List of all the services near the expiring date
	 * @return array
	 */
	public function ServicesExpiration() {
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$data = $ns->customer;
			return array ('records' => Products::getExpiringProducts($data['customer_id'], $ns->langid), 'actions' => array ('/services/edit/id/' => 'Show' ) );
		} else {
			return null;
		}
	}
	
	/**
	 * ServicesExpiration
	 * List of all the services near the expiring date
	 * @return array
	 */
	public function Last() {
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$data = $ns->customer;
			return array ('records' => Tickets::Last($data['customer_id']), 'actions' => array ('/tickets/edit/id/' => 'Show' ) );
		} else {
			return null;
		}
	}
	
}
