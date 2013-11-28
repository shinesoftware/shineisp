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
		$registry = Shineisp_Registry::getInstance ();
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
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function doAction() {
		
		$request = $this->getRequest ();
		$q = $request->getParam ( 'q' );
		$results = array();
		
		$q = strtolower ( $q );
		if (empty($q)) {
			die(json_encode(array($this->translator->translate('No Records'))));
		}
		
		$cms  = CmsPages::getList ();
		foreach ( $cms as $key => $value ) {
		    if (strpos ( strtolower ( $value ), $q ) !== false) {
		        $results[] = array(
		                'icon' => 'glyphicon-file',
		                'section' => $this->translator->translate ( 'Cms' ),
		                'value' => $value,
		                'url' => "/admin/cmspages/edit/id/$key",
		                'tokens' => explode( ' ', $value )
		        );
		    }
		}
		
		$customers = Customers::getList ();
		if (! empty ( $customers )) {
			foreach ( $customers as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					$results[] = array(
					        'icon' => 'glyphicon-user',
					        'section' => $this->translator->translate ( 'Customer' ),
					        'value' => $value,
					        'url' => "/admin/customers/edit/id/$key",
					        'tokens' => explode( ' ', $value )
					);
				}
			}
		}
		
		$domains = Domains::getList ();
		if (! empty ( $domains )) {
			foreach ( $domains as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					$results[] = array(
					        'icon' => 'glyphicon-globe',
					        'section' => $this->translator->translate ( 'Domain' ),
					        'value' => $value,
					        'url' => "/admin/domains/edit/id/$key",
					        'tokens' => explode( ' ', $value )
					);
				}
			}
		}
		
		$products = Products::getList ();
		if (! empty ( $products )) {
			foreach ( $products as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					$results[] = array(
					        'icon' => 'glyphicon-barcode',
					        'section' => $this->translator->translate ( 'Product' ),
					        'value' => $value,
					        'url' => "/admin/products/edit/id/$key",
					        'tokens' => explode( ' ', $value )
					);
				}
			}
		}
		
		$orders = Orders::getList ();
		if (! empty ( $orders )) {
			foreach ( $orders as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					$results[] = array(
					        'icon' => 'glyphicon-briefcase',
					        'section' => $this->translator->translate ( 'Order' ),
					        'value' => $value,
					        'url' => "/admin/orders/edit/id/$key",
					        'tokens' => explode( ' ', $value )
					);
				}
			}
		}
		
		$ordersitems = OrdersItems::getItemsListbyDescription ( $q );
		if (! empty ( $ordersitems )) {
			foreach ( $ordersitems as $key => $value ) {
				$results[] = array(
				        'icon' => 'glyphicon-briefcase',
				        'section' => $this->translator->translate ( 'Order Item' ),
				        'value' => $value,
				        'url' => "/admin/ordersitems/edit/id/$key",
				        'tokens' => explode( ' ', $value )
				);
			}
		}
		
		$tickets = Tickets::getList ( );
		if (! empty ( $tickets )) {
			foreach ( $tickets as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					$results[] = array(
					        'icon' => 'glyphicon-check',
					        'section' => $this->translator->translate ( 'Ticket' ),
					        'value' => $value,
					        'url' => "/admin/tickets/edit/id/$key",
					        'tokens' => explode( ' ', $value )
					);
				}
			}
		}
		
		$wiki = Wiki::getList ( );
		if (! empty ( $wiki )) {
			foreach ( $wiki as $key => $value ) {
				if (strpos ( strtolower ( $value ), $q ) !== false) {
					$results[] = array(
					        'icon' => 'glyphicon-question-sign',
					        'section' => $this->translator->translate ( 'Wiki' ),
					        'value' => $value,
					        'url' => "/admin/wiki/edit/id/$key",
					        'tokens' => explode( ' ', $value )
					);
				}
			}
		}
		
		$ticket = TicketsNotes::getItemsNote ($q);
		foreach ( $ticket as $key => $value ) {
			$results[] = array(
			        'icon' => 'glyphicon-question-sign',
			        'section' => $this->translator->translate ( 'Ticket Notes' ),
			        'value' => $value,
			        'url' => "/admin/wiki/tickets/id/$key",
			        'tokens' => explode( ' ', $value )
			);
		}
		
		die(json_encode($results));
	}

}
    
