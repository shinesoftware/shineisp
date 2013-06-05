<?php

class CommonController extends Shineisp_Controller_Default {

	/**
	 * callbackAction
	 * This method isn't called from the project 
	 * but it is called from a bank gateway service 
	 * in order to set as payed the order processed
     *	 
	 * IMPORTANT:
	 * This method was within the /default/orders controller and it has been moved here
	 * because the access to the /default/orders is denied without an authentication process
	 * /default/common controller is accessible without login process.
	 */
	public function callbackAction() {
		$request = $this->getRequest ();
		$response = $request->getParams ();
		
		if (! empty ( $response ['custom'] ) && is_numeric ( trim($response ['custom'] ))) {
			
			// Getting the md5 value in order to match with the class name.
			$classrequest = $request->gateway;
			
			// Orderid back from the bank
			$order_id = trim($response ['custom']);
			
			// Get the bank selected using the MD5 code 
			$bank = Banks::findbyMD5 ( $classrequest );
			if (! empty ( $bank [0] ['classname'] )) {
				if (! empty ( $bank [0] ['classname'] ) && class_exists ( $bank [0] ['classname'] )) {
					
					$class = $bank [0] ['classname'];
					$payment = new $class ( $response ['custom'] );
					
					// Check if the method "Response" exists in the Payment class and send all the bank information to the payment module
					if (method_exists ( $class, "Response" )) {
						Shineisp_Commons_Utilities::logs ( "Callback called: $class\nParameters: " . json_encode ( $response ), "payments.log" );
						$payment->Callback ( $response );
					}
				}
			}
		}
		die ();	
	}
		
	public function deletetagAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$back = $this->getRequest ()->getParam ( 'back' );
		if (is_numeric ( $id )) {
			$back = str_replace ( '_', "/", $back );
			Tags::DeleteTagConnection ( $id );
		}
		header ( 'location: ' . $back );
		die ();
	}
	
	public function tagsAction() {
		$data = array ();
		$i = 0;
		
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (empty($NS->customer)) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
		$customer = $NS->customer;
		
		$tags = Tags::findbyCustomerID ( $customer ['customer_id'] );
		$q = $this->getRequest ()->getParam ( 'q' );
		foreach ( $tags as $id => $tag ) {
			if (strpos ( strtolower ( $tag ), $q ) !== false) {
				$data [$i] ['id'] = $tag;
				$data [$i] ['name'] = $tag;
				$i ++;
			}
		}
		echo json_encode ( $data );
		die ();
	}
	
	public function gettagsAction() {
		$data = array ();
		$i = 0;
		
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (empty($NS->customer)) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
		$customer = $NS->customer;
		
		$tags = Tags::findbyCustomerID ( $customer ['customer_id'] );
		$q = $this->getRequest ()->getParam ( 'q' );
		foreach ( $tags as $id => $tag ) {
			$data [$i] ['id'] = $tag;
			$data [$i] ['name'] = $tag;
			$i ++;
		}
		echo json_encode ( $data );
		die ();
	}
	
	/**
	 * Search a product in the archive
	 * 
	 * @return string 
	 */
	public function searchAction() {
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$request = $this->getRequest ();
		
		$q = $request->getParam ( 'q' );
		$q = strtolower ( $q );
		if (! $q) {
			return;
		}
		
		$products = Products::search($q, null, true);
		foreach ( $products as $product ) {
			if(!empty($product['uri'])){
				$img = !empty($product['imgpath']) ? $this->view->image($product['name'], $product['imgpath'], array('width'=>100, 'alt' => $product['name'], 'title' => $product['name'])) : "";
				echo $product['uri'] . "|" . $img . "<span class='ac_layout'><b>". strtoupper($product['name']) . "</b><br/>" . Shineisp_Commons_Utilities::truncate(strip_tags($product['shortdescription']), 200, "...", true, true) . "<br/>" . $product['keywords'] ."</span>|\n";
			}
		}
		
		die ();
	}
	
	
	/**
	 * Search a domain within the customer list
	 * 
	 * 
	 * @return string
	 */
	public function searchdomainAction() {
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$request = $this->getRequest ();
		
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (empty($NS->customer)) {
			die ();
		}
		
		$q = $request->getParam ( 'q' );
		$customer = $NS->customer;
		$q = strtolower ( $q );
		if (! $q) {
			return;
		}
		
		$domains = Domains::findbyUserId ( $customer ['customer_id'], "domain_id, CONCAT(domain, '.', tld) as domain" );
		foreach ( $domains as $domain ) {
			if (strpos ( strtolower ( $domain['domain'] ), $q ) !== false) {
				echo $domain['domain_id'] . "|" . strtoupper($domain['domain']) . "|\n";
			}
		}
		
		die ();
	}
	
	/*
     *  Check the domain availability
     */
	public function checkdomainAction() {
		$currency = Zend_Registry::getInstance ()->Zend_Currency;
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$request = $this->getRequest ();
		try{
			if ($request->getPost ()) {
				$params = $request->getPost ();
				
				$available = Domains::check_availability($params['name'], $params['tld']);
				$data = DomainsTlds::getAllInfo($params['tld']);
				
				$tldName = $data['WhoisServers']['tld'];

				// Create the domain name
				$domain = $params['name'] . "." . $tldName;

				if($available){
					$price = $data['registration_price'];  // Get the price of the product
				}else{
					$price = $data['transfer_price'];
				}
				
				if(!empty($data['Taxes']['percentage']) && is_numeric($data['Taxes']['percentage'])){
					$formatPrice = $currency->toCurrency($price * ($data['Taxes']['percentage'] + 100) / 100, array('currency' => Settings::findbyParam('currency')));
				}else{
					$formatPrice = $currency->toCurrency($price, array('currency' => Settings::findbyParam('currency')));
				}
				
				// Format the price number
				$strprice = $translator->translate('just') . " $formatPrice!";

				// Create the message
				$mex = $available ? $translator->translate('The domain is available for registration') : $translator->translate("The domain is not available for registration but if you are the domain's owner and you can transfer it!") ;
				
				// Reply with JSON code
				die(json_encode(array('available' => $available, 'name' => $params['name'], 'tld' => $params['tld'], 'price' => $strprice, 'domain' => $domain, 'mex'=> $mex)));
			}
		}catch (Exception $e){
			die(json_encode(array('mex' => $e->getMessage ())));
		}
	}	
	
	/**
	 * Create a text image
	 *  
	 */
	public function imgtextAction() {
		$string = $this->getRequest()->getParam('q');
		$width = is_numeric($this->getRequest()->getParam('w')) ? $this->getRequest()->getParam('w') : 100;
		$height = is_numeric($this->getRequest()->getParam('h')) ? $this->getRequest()->getParam('h') : 100;
		$fontname = $this->getRequest()->getParam('fn') ? $this->getRequest()->getParam('fn') : "arial.ttf";
		$fontsize = is_numeric($this->getRequest()->getParam('f')) ? $this->getRequest()->getParam('f') : 14;
		if(!empty($string)){
			Shineisp_Commons_ImgText::create($string, $width, $height, $fontname, $fontsize);
		}else{
			Shineisp_Commons_ImgText::create("-");
		}
		die;
	}
	
	
}