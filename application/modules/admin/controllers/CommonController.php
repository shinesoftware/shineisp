<?php

/**
 * CommonController
 * Manage the common operations
 * @version 1.0
 */

class Admin_CommonController extends Zend_Controller_Action {
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function indexAction() {
		$this->_helper->redirector ( 'index', 'index', 'admin' );
	}
	
	/*
	 *  check if the fastlink is present in the database
	 */
	public function checkfastlinkAction() {
		$request = $this->getRequest ();
		$fastlink = $request->getParam ( 'id' );
		$link_exist = Fastlinks::findbyCode ( $fastlink );
		if (count ( $link_exist ) > 0) {
			echo 1;
		} else {
			echo 0;
		}
		die ();
	}
	
	/*
	 *  get all the resources available starting from the method of payment
	 */
	public function getresourceAction() {
		$items = array ();
		$request = $this->getRequest ();
		$paymentmethod = $request->getParam ( 'id' );
		$resources = PaymentsResources::findAllbyMethodId ( $paymentmethod, false, true );
		foreach ( $resources as $c ) {
			$items [$c ['resource_id']] = $c ['resource'];
		}
		die ( json_encode ( $items ) );
	}
	
	/*
	 * children: [
                {title: "Item 1", key: "node1"},
                {title: "Folder 2", isFolder: true, key: "node2",
                    children: [
                        {title: "Sub-item 2.1", key: "node2.1"},
                        {title: "Sub-item 2.2", key: "node2.2"}
                    ]
                },
                {title: "Item 3", key: "node3"}
            ]
	 */
	public function getajaxcategoriesAction() {
		$items = $this->createCategoryTree ( 0 );
		die ( json_encode ( $items ) );
	}
	
	private function createCategoryTree($id) {
		$cats = array();
		$isfolder = false;
		$items = ProductsCategories::getbyParentId ($id);
		foreach ( $items as $category ) {
			$subcategory = $this->createCategoryTree ( $category['category_id'] );
			$isfolder = ($subcategory) ? true : false;
			if($subcategory){
				$cats[] = array('key' => $category['category_id'], 'title' => $category['name'], 'isFolder' => $isfolder, 'children'=>$subcategory);
			}else{
				$cats[] = array('key' => $category['category_id'], 'title' => $category['name']);
			}
		}
		return $cats;
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
		
		$q = trim($q);
		
		$customers = Customers::getList ();
		foreach ( $customers as $key => $value ) {
			if (strpos ( strtolower ( $value ), $q ) !== false) {
				echo "$key|$value|customers|" . $this->translator->translate ( 'Customers' ) . "\n";
			}
		}
		
		$customers = Customers::getEmailList ();
		foreach ( $customers as $key => $value ) {
			if (strpos ( strtolower ( $value ), $q ) !== false) {
				echo "$key|$value|customers|" . $this->translator->translate ( 'Customer email' ) . "\n";
			}
		}
		
		$domains = Domains::getList ();
		foreach ( $domains as $key => $value ) {
			if (strpos ( strtolower ( $value ), $q ) !== false) {
				echo "$key|$value|domains|" . $this->translator->translate ( 'Domains' ) . "\n";
			}
		}
		
		$products = Products::getList ();
		foreach ( $products as $key => $value ) {
			if (strpos ( strtolower ( $value ), $q ) !== false) {
				echo "$key|$value|products|" . $this->translator->translate ( 'Products' ) . "\n";
			}
		}
		
		$orders = Orders::getList ();
		foreach ( $orders as $key => $value ) {
			if (strpos ( strtolower ( $value ), $q ) !== false) {
				echo "$key|$value|orders|" . $this->translator->translate ( 'Orders' ) . "\n";
			}
		}
		
		$wiki = Wiki::getList ();
		foreach ( $wiki as $key => $value ) {
			if (strpos ( strtolower ( $value ), $q ) !== false) {
				echo "$key|$value|wiki|" . $this->translator->translate ( 'Wiki' ) . "\n";
			}
		}
		
		die ();
	}

}
    
