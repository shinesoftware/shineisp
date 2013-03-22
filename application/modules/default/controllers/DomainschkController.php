<?php

class DomainschkController extends Zend_Controller_Action {
	protected $customer;
	protected $domains;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	/**
	 * indexAction
	 * Redirect the user to the list action
	 * @return unknown_type
	 */
	public function indexAction() {
		$form = new Default_Form_DomainsinglecheckerForm ( array ('action' => '/domainschk/check', 'method' => 'post' ) );
		$this->view->form = $form;
		
	}
	
	/**
	 * Save all the domain selection
	 * @return unknown_type
	 */
	public function saveAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$params = $this->getRequest()->getParams();
		
		if (empty($NS->customer)) {
			$profile = $NS->customer;
			$customerid = $profile['customer_id'];
		}else{
			$customerid = NULL;
		}
		
		if(empty($params['tlds'])){
			// Redirect the user to the domain check page 
			$this->_helper->redirector ( 'index', 'domainschk', 'default', array('mex' => 'You have to check one of the domain tlds.', 'status' => 'error'));
		}
		
		$domain = $params['domain'];
		$tlds = $params['tlds'];
		$authinfo = !empty($params['authinfo']) ? $params['authinfo'] : null;
		
		// Clear the temporary list before adding the new one
		DomainsBulk::clear(Zend_Session::getId());
		
		// Adding the domain in the temporary domains bulk table
		if(!empty($tlds) && !empty($domain)){
			for ($i=0; $i<count($tlds);$i++) {
				$isavailable = Domains::check_availability($domain, $tlds[$i]);
				$authinfocode = !empty($authinfo[$i]) ? $authinfo[$i] : null;
				DomainsBulk::add_domain($domain, $tlds[$i], $isavailable, $customerid, $authinfocode);
			}
		}
		
		// Redirect the user to the order creation page. 
		$this->_helper->redirector ( 'createorder', 'domainschk', 'default');
			
	}
	
	/**
	 * Create a new order on the fly
	 */
	public function createorderAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (!empty($NS->customer)) {
			$profile = $NS->customer;
			
			// Destroy the redirect option
			unset($NS->goto);
			
			// Get the temporary domains
			$domains = DomainsBulk::findbySession(Zend_Session::getId());
			
			if(!empty($domains)){
				
				// Create the base order document
				$theOrder = Orders::create($profile['customer_id']);
			
				foreach ($domains as $domain) {
					
					$Thedomain = $domain['domain'] . "." . $domain['DomainsTlds']['DomainsTldsData'][0]['name'];
					
					$action = $domain['isavailable'] ? "registerDomain" : "transferDomain" ;
					$price = $domain['price'];
					$cost = $domain['cost'];
					$authcode = !empty($domain['authinfo']) ? $domain['authinfo'] : null;
					
					// Create the order item
					Orders::addOrderItem($theOrder['order_id'], $Thedomain, 1, 3, $price, $cost, 0, array ('domain' => $Thedomain, 'action' => $action, 'authcode' => $authcode, 'tldid' => $domain['tld_id']));
				}
				
				// Send the order to the customer 
				Orders::sendOrder($theOrder['order_id']);

				// Clear the temporary list before adding the new one
				DomainsBulk::clear(Zend_Session::getId());
				
				// Redirect the user to the order detail page
				$this->_helper->redirector ( 'edit', 'orders', 'default', array ('id' => $theOrder['order_id'], 'mex' => 'Order created successfully', 'status' => 'success' ) );
			}
			
		}else{
			
			// Create the redirection
			$NS->goto = array('action' => 'createorder', 'controller' => 'domainschk', 'module' => 'default', 'options' => array());
			 
			$this->_helper->redirector ( 'signup', 'customer', 'default', array ('mex' => 'You have to login or create a new account profile to go on.', 'status' => 'success' ) );
		}
		
	}
	

	/*
     *  Check the domain availability
     */
	public function checkAction() {
		$currency = new Zend_Currency();
		$form = new Default_Form_DomainsinglecheckerForm ( array ('action' => '/domainschk/check', 'method' => 'post' ) );
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$request = $this->getRequest ();
		try{
			if ($request->getPost ()) {
				$params = $request->getPost ();
				
				
				// Delete spaces, symbols, dots, commas, spaces, etc...
				$params['name'] = Shineisp_Commons_UrlRewrites::format($params['name']);

				$available = Domains::check_availability($params['name'], $params['tld']);
				
				$data = DomainsTlds::getAllInfo($params['tld']);
				
				$tldName = $data['DomainsTldsData'][0]['name'];
				
				// Create the domain name
				$domain = $params['name'] . "." . $tldName;
				
				// Domain Price
				$price = ($available) ? $data['registration_price'] : $data['transfer_price'];
				
				$taxpercent = $data['Taxes']['percentage'];
				
				// Format the price number
				$strprice = $translator->translate('just') ." ". $currency->toCurrency($price * ($taxpercent + 100) / 100, array('currency' => Settings::findbyParam('currency')));

				// Create the message
				$mex = $available ? $translator->translate('The domain is available for registration') : $translator->translate("The domain is not available for registration but if you are the domain's owner and you can transfer it!") ;
				
				$this->view->form = $form;
				$this->view->results = array('available' => $available, 'name' => $params['name'], 'tld' => $params['tld'], 'price' => $strprice, 'domain' => $domain, 'mex'=> $mex);
				$this->view->suggestions = $this->chktlds($params['name'], array($params['tld']));
			}
		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'domainschk', 'default', array ('mex' => $e->getMessage(), 'status' => 'error' ) );
		}
		
		$this->_helper->viewRenderer('result');
	}


	/**
	 * Check all the tld domain extensions
	 * @param string $name
	 * @param array $exluded (exclude a tld extension)
	 */
	private function chktlds($name, $exluded=""){
		$result = array();
		$currency = new Zend_Currency();
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$tlds = DomainsTlds::getAll();
		
		foreach ($tlds as $tld) {
			
			if(!in_array($tld['tld_id'], $exluded)){
				$available = Domains::check_availability($name, $tld['tld_id']);
				
				// Create the domain name
				$domain = $name . "." . $tld['DomainsTldsData'][0]['name'];
				
				// Domain Price
				$price = ($available) ? $tld['registration_price'] : $tld['transfer_price'];
				
				$taxpercent = $tld['Taxes']['percentage'];
				
				// Format the price number
				$strprice = $translator->translate('just') ." ". $currency->toCurrency($price * ($taxpercent + 100) / 100, array('currency' => Settings::findbyParam('currency')));
				
				// Create the message
				$mex = $available ? $translator->translate('The domain is available for registration') : $translator->translate("The domain is not available for registration but if you are the domain's owner and you can transfer it!") ;
				
				$result[] = array('available' => $available, 'name' => $name, 'tld' => $tld['tld_id'], 'price' => $strprice, 'domain' => $domain, 'mex'=> $mex);
			}
		}
			
		return $result;
	}
}