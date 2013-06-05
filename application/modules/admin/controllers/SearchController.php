<?php

/**
 * SearchController
 * Manage the isp profile
 * @version 1.0
 */

class Admin_SearchController extends Shineisp_Controller_Admin {
	
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$registry = Zend_Registry::getInstance ();
		$this->translator = $registry->Zend_Translate;
	}
	
	public function indexAction() {
		$this->_helper->redirector ( 'index', 'index', 'admin' );
	}
	
	public function gotoAction() {
		$request = $this->getRequest ();
		$id = $request->getParam ( 'id' );
		$module = $request->getParam ( 'mod' );
		$this->_helper->redirector ( 'edit', $module, 'admin', array ('id' => $id ) );
		die ();
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function doAction() {
		
		$request = $this->getRequest ();
		$q = $request->getParam ( 'q' );
		
		$q = strtolower ( $q );
		if (! $q) {
			return;
		}
		
		$cms = CmsPages::getList ();
		foreach ( $cms as $key => $value ) {
			if (strpos ( strtolower ( $value ), $q ) !== false) {
				echo "$key|$value|cmspages|" . $this->translator->translate ( 'Cms' ) . "\n";
			}
		}
					
		
		$customers = Customers::getList ();
		if (! empty ( $customers )) {
			foreach ( $customers as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					echo "$key|$value|customers|" . $this->translator->translate ( 'Customer' ) . "\n";
				}
			}
		}
		
		$domains = Domains::getList ();
		if (! empty ( $domains )) {
			foreach ( $domains as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					echo "$key|$value|domains|" . $this->translator->translate ( 'Domain' ) . "\n";
				}
			}
		}
		
		$products = Products::getList ();
		if (! empty ( $products )) {
			foreach ( $products as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					echo "$key|$value|products|" . $this->translator->translate ( 'Product' ) . "\n";
				}
			}
		}
		
		$orders = Orders::getList ();
		if (! empty ( $orders )) {
			foreach ( $orders as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					echo "$key|$value|orders|" . $this->translator->translate ( 'Order' ) . "\n";
				}
			}
		}
		
		$ordersitems = OrdersItems::getItemsListbyDescription ( $q );
		if (! empty ( $ordersitems )) {
			foreach ( $ordersitems as $key => $value ) {
				echo "$key|$value|orders|" . $this->translator->translate ( 'Order' ) . "\n";
			}
		}
		
		$tickets = Tickets::getList ( );
		if (! empty ( $tickets )) {
			foreach ( $tickets as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					echo "$key|$value|tickets|" . $this->translator->translate ( 'Ticket' ) . "\n";
				}
			}
		}
		
		$wiki = Wiki::getList ( );
		if (! empty ( $wiki )) {
			foreach ( $wiki as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					echo "$key|$value|wiki|" . $this->translator->translate ( 'Wiki' ) . "\n";
				}
			}
		}
		
		$ticket = TicketsNotes::getItemsNote ($q);
		foreach ( $ticket as $key => $value ) {
			echo "$key|$value|tickets|" . $this->translator->translate ( 'Tickets' ) . "\n";
		}
		
		die ();
	}

}
    
