<?php

class NotfoundController extends Shineisp_Controller_Default {
	
	
	public function preDispatch() {
		 $this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
	}
	
	public function indexAction() {
		$items = array();
		$ns = new Zend_Session_Namespace ();
		$locale = $ns->lang;
		$uri = $this->getRequest ()->getParam('uri');
		
		
		if(!empty($uri)){
			$products = Products::getProductsbyText($uri);
			foreach ($products as $product){
				$items[] = array('title' => $product['name'], 'description' => $product['shortdescription'], 'url' => '/' . $product['uri'] . '.html');	
			}
		}
		
		$this->view->items = $items;
	}

}