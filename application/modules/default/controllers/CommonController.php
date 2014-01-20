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
	 * Search a domain within the customer list
	 * 
	 * 
	 * @return string
	 */
	public function searchdomainAction() {
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
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
	
	
	
	/**
	 * bulkdomainsorderAction
	 * Check the availability for each domain.
	 */
	
	public function bulkdomainsorderAction() {
		$form = new Default_Form_BulkdomainsorderForm ( array ('action' => '/common/createbulkorder', 'method' => 'post' ) );
		$session = new Zend_Session_Namespace ( 'Default' );
		if($session->customer){
			$domains = DomainsBulk::findbyCustomerID ( $session->customer ['customer_id'] );
		}else{
			$domains = DomainsBulk::findbySession( Zend_Session::getId() );
		}
		$this->view->domains = $domains;
		$this->view->form = $form;
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	/**
	 * Renew a group of domains selected
	 *
	 *
	 * @return void
	 */
	public function createbulkorderAction() {
		$session = new Zend_Session_Namespace ( 'Default' );
	
		$mex = "";
		$items ['authcode'] = $this->getRequest ()->getParam ( 'authcode' );
		$items ['domains'] = $this->getRequest ()->getParam ( 'item' );
		$items ['billing_id'] = $this->getRequest ()->getParam ( 'billing_id' );
	
		if (is_array ( $items )) {
			try {
	
				if (empty ( $session->cart ) ) {
					$session->cart = new Cart();
					$session->cart->setCustomer($this->customer ['customer_id']);
				}
	
				for($i = 0; $i < count ( $items ['domains'] ); $i ++) {
					$data = DomainsBulk::find ( $items ['domains'] [$i] );
					$action = $data['isavailable'] ? "registerDomain" : "transferDomain";
					$params = array('domain' => $data['domain'], 'tld' => $data['tld_id'], 'action' => $action, 'authcode' => $items['authcode'][$i]);
						
					$session->cart->addDomain($data['domain'], $data['tld_id'], $action);
						
				}
	
				$this->_helper->redirector ( 'summary', 'cart', 'default', array ('mex' => 'The domains have been added in the cart' ) );
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'list', 'domains', 'default', array ('mex' => 'A problem has been occurred during the creation of the order.', 'status' => 'danger' ) );
			}
		}
		return false;
	}
	
	
	/**
	*  Check the domain availability
	*  @return template
	*/
	public function bulkdomainsAction() {
		$request = $this->getRequest ();
		$session = new Zend_Session_Namespace ( 'Default' );
		
		
		$i = 0;
		try {
			$form = new Default_Form_BulkdomainsForm ( array ('action' => '/common/bulkdomains', 'method' => 'post' ) );
			if ($request->getPost ()) {
				if ($form->isValid ( $request->getPost () )) {
					$params = $form->getValues ();
						
					if (! empty ( $params ['domains'] )) {
						$checker = new Shineisp_Commons_DomainChecker ();
						$tlds = $request->getParam ( 'tlds' );
						
						// Clear the temporary domains of the customer
						if($session->customer){
							$customerId = $session->customer ['customer_id'];
							DomainsBulk::findbyCustomerID ( $customerId )->delete ();
						}else{
							$customerId = null;
						}
						
						$domains = explode ( "\n", $params ['domains'] );
	
						foreach ($domains as $domain){
							foreach ($tlds as $tld){
								$tldinfo = DomainsTlds::getAllInfo($tld);
								$domain = Shineisp_Commons_UrlRewrites::format($domain);
								$domainame = $domain . "." . $tldinfo['DomainsTldsData'][0]['name'];
								$isAvailable = $checker->checkDomainAvailability ( $domainame );
								DomainsBulk::add_domain($domainame, $tld, $isAvailable, $customerId);
							}
						}
	
						$this->_helper->redirector ( 'bulkdomainsorder', 'common', 'default' );
					}
				}
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => $e->getMessage (), 'status' => 'danger' ) );
		}
	
		$this->view->form = $form;
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	/**
	 * Buying a domain by querystring
	 * 
	 * @param integer tld (DomainsTlds [tld_id])
	 * @param string name (Domain name choosen)
	 */
	public function buyAction() {
		$session = new Zend_Session_Namespace ( 'Default' );
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$request = $this->getRequest ();
		try{
			$params = $request->getParams();
				
			$rs = DomainsTlds::getAllInfo($params['tld']);
				
			if(!empty($rs)){
				$tldName = $rs['WhoisServers']['tld'];
	
				// Create the domain name
				$domain = $params['name'] . "." . $tldName;
				$domainaction = !empty($params['do']) && $params['do'] == "register" ? "registerDomain" : "transferDomain";
	
				if (empty ( $session->cart ) ) {
					$session->cart = new Cart();
					$session->cart->setCustomer($this->customer ['customer_id']);
				}
	
				$session->cart->addDomain($domain, $rs['tld_id'], $domainaction);
	
				$this->_helper->redirector ( 'summary', 'cart', 'default', array ( 'mex' => $translator->translate('The domain has been added in your order'), 'status' => 'success' ) );
			}else{
				$this->_helper->redirector ( 'summary', 'cart', 'default', array ('mex' => $translator->translate('The selected Domain TLD has not been found.'), 'status' => 'danger' ) );
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => $e->getMessage (), 'status' => 'danger' ) );
		}
	}
	
	/*
     *  Check the domain availability
     */
	public function checkdomainAction() {
		$currency = Shineisp_Registry::getInstance ()->Zend_Currency;
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
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
				$mex = $available ? $translator->translate('The domain is available for registration') : $translator->translate("The domain is unavailable for registration, but if you are the domain owner, you can transfer it!") ;
				
				// Reply with JSON code
				die(json_encode(array('available' => $available, 'name' => $params['name'], 'tld' => $params['tld'], 'price' => $strprice, 'domain' => $domain, 'mex'=> $mex)));
			}
		}catch (Exception $e){
			die(json_encode(array('mex' => $e->getMessage ())));
		}
	}	
	
}