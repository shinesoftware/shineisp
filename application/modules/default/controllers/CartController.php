<?php

class CartController extends Shineisp_Controller_Default {
	protected $customer;
	protected $session;
	protected $cart;
	protected $translator;
	protected $currency;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$session 	= new Zend_Session_Namespace ( 'Default' );
		$cart 		= Zend_Registry::isRegistered('cart');
		
		// Create the cart object
		if(empty($cart)){
			Zend_Registry::set('cart', new Cart());
		}
		
		if (empty($this->customer)) {
			$this->customer = $session->customer;
		}
		
		if (empty($this->session)) {
			$this->session = $session;
		}
		
		$this->currency = Shineisp_Registry::get ( 'Zend_Currency' );
		$this->translator = Shineisp_Registry::get ( 'Zend_Translate' );

		$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
	}
	
	/**
	 * Redirect the user to the list action
	 */
	public function indexAction() {
		
		try{
			
			$session 	= new Zend_Session_Namespace ( 'Default' );
		
			if (empty ( $session->cart ) || $session->cart->isEmpty()) {
				$session->cart = new Cart();
			}
			
			// Check if the product is present in the cart
			if ($session->cart->checkIfHostingProductIsPresentWithinCart ()) {
				$this->_helper->redirector ( 'domain' );
			} else {
				$this->_helper->redirector ( 'contacts' );
			}

		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
	}
	
	/*
	 * Clear all the items from the cart
	*/
	public function clearAction() {
		$session 	= new Zend_Session_Namespace ( 'Default' );
		
		if (!empty ( $session->cart ) && !$session->cart->isEmpty()) {
			$session->cart->clearAll();
		}
		
		$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => 'Cart items have been deleted.') );
	}
	
	/*
	 * Remove an item from the cart
	*/
	public function removeAction() {
		$session 	= new Zend_Session_Namespace ( 'Default' );
		
		// Get the sent parameters
		$uid = $this->getRequest ()->getParam ('uid');
		
		if (!empty ( $session->cart ) && !$session->cart->isEmpty()) {
			if($session->cart->deleteItem($session->cart->getItemByUid($uid))){
				$this->_helper->redirector ( 'summary', 'cart', 'default', array('mex' => 'Cart item has been deleted.') );
			}else{
				$this->_helper->redirector ( 'summary', 'cart', 'default', array('mex' => 'Cart item has been not deleted.') );
			}
		}
		
		$this->_helper->redirector ( 'summary', 'cart', 'default', array('mex' => 'Cart item has been not deleted.') );
	}
	
	/**
	 * Check the product and redirect the user to the right destination
	 */
	public function addAction() {
		
		try{
			$isVATFree = false;
			$session 	= new Zend_Session_Namespace ( 'Default' );
		
			if (empty ( $session->cart )) {
				$session->cart = new Cart();
			}
			
			// Get the sent parameters
			$request = $this->getRequest ()->getParams ();
			
			if (! empty ( $request ['product_id'] ) && is_numeric ( $request ['product_id'] )) {
				
				// Check the quantity value posted
				if (! empty ( $request ['quantity'] ) && is_numeric ( $request ['quantity'] )) {
					
					// Get all the info about the product selected
					$product = Products::getAllInfo( $request ['product_id'] );
	
					// Check if the user has been logged in
					if (!empty($this->customer['customer_id'])) {
						
						// Set the customer for the active cart
						$session->cart->setCustomer($this->customer['customer_id']);
						
						// Check if the user is VAT free
						$isVATFree = Customers::isVATFree($session->cart->getCustomerId());
					}
					
					$priceInfo = Products::getPriceSelected($request ['product_id'], $request['term'], $isVATFree);
					
					$item = new CartItem();
					
					if($session->cart->getItem($request ['product_id'])){
						$item = $session->cart->getItem($request ['product_id']);
						$session->cart->updateItem($item, $item->getQty() + 1);
					} else { // Add the items to the cart:
						
						$item->setId($request ['product_id'])
							->setSku($product['sku'])
							->setName($product['ProductsData'][0]['name'])
							->setCost($product['cost'])
							->setTerm($request ['term'])
							->setQty($request ['quantity'])
							->setUnitprice($priceInfo['unitprice'])
							->setTaxId($product['tax_id'])
							->setSetupfee($priceInfo['setupfee'])
							->setType(Products::getProductType($request ['product_id']));
						
						$session->cart->addItem($item);
					}
					
					// Check if a hosting product is present in the cart
					if ($session->cart->getCustomer ()) {
						$this->_helper->redirector ( 'summary', 'cart', 'default' );
					} else {
						$this->_helper->redirector ( 'contacts' );
					}
				}
				
				// Quantity is not correct and the user is redirected to the homepage
				$this->_helper->redirector ( 'index', 'index', 'default' );
			}
			
		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
	}

	/*
	 * Get the customer information
	*/
	public function contactsAction() {
	
		$request = $this->getRequest ();
		$session = new Zend_Session_Namespace ( 'Default' );
		$this->getHelper ( 'layout' )->setLayout ( '1column' );

		if (empty ( $session->cart ) || $session->cart->isEmpty()) {
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
		}
			
		// Check if there is a domain service within the cart.
		// If a domain is present we have to create a nic-handle in order to register the
		// customer in the remote registrar database
		$hasdomain = $session->cart->hasDomain ();
			
		// Check if the user has been logged in
		if (!empty($this->customer)) {
			$customer = $this->customer;

			// Set the customer for the active cart
			$session->cart->setCustomer($customer['customer_id']);

			// Check if the customer is a reseller
			if (! empty ( $customer ['isreseller'] ) && $customer ['isreseller']) {
				$session->cart->setReseller($customer['customer_id']);
				$this->_helper->redirector ( 'reseller', 'cart', 'default' );
			} else {
				$session->cart->removeReseller();
			}

			$this->view->contact = $customer;
			$this->_helper->viewRenderer ( 'contactlogged' );
				
		} else {
			// Clean the session vars
			$session->cart->removeCustomer();
			$session->cart->removeReseller();
		}
			
		// Create the sidebar if the cart has products
		$items = $session->cart->getItems();
		$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
			
		$this->view->hasdomain = $hasdomain;

		// Create the form
		$form = new Default_Form_CustomerForm ( array ('action' => "/cart/contacts", 'method' => 'post' ) );
		$form->getElement ( 'submit' )->setLabel ( 'Continue Order' );
		$form->populate ( array('country_id' => 82, 'contacttypes' => 1) ); // Set Italy as default and the first contact type id 1 as telephone
			
		if ($session->cart->getCustomer()) {
			$form->populate ( $session->cart->getCustomer() );
		}
			
		// If the product/service include a domain we need more information
		if ($hasdomain === false){
			$form->getElement ( 'gender' )->setRequired ( false );
			$form->getElement ( 'gender' )->setRegisterInArrayValidator ( false );
			$form->getElement ( 'birthdate' )->setRequired ( false );
			$form->getElement ( 'birthplace' )->setRequired ( false );
			$form->getElement ( 'birthdistrict' )->setRequired ( false );
			$form->getElement ( 'birthcountry' )->setRequired ( false );
			$form->getElement ( 'birthnationality' )->setRequired ( false );
		}
			
		$this->view->form = $form;
			
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();

			if ($form->isValid ( $params )) {
					
				// Send the confirmation email to the customer
				$params['welcome_mail'] = true;
					
				// Create a customer or get his ID
				$result = Customers::Create( $params );
					
				if (is_numeric ( $result )) {

					// Do the login
					if($this->doLogin ( $result )){
						$this->_helper->redirector ( 'summary', 'cart', 'default', array ('mex' => 'Well done! Now you have to choose your preferite payment method.', 'status' => 'success' ) );
					}
				} else {
					$this->view->mex = $result;
					$this->view->mexstatus = "error";
				}
			}
		}
	}
	

	/*
	 * Show the domain checker form
	 */
	public function domainAction() {
		
		try{
			
			$session = new Zend_Session_Namespace ( 'Default' );
			
			// Create a new form domain checker
			$form = new Default_Form_DomaincheckerForm ( array ('action' => "/cart/checkdomain", 'method' => 'post' ) );
			$this->view->form = $form;
			
			// Create the sidebar if the cart has products
			if (! empty ( $session->cart ) && false === $session->cart->isEmpty()) {
				if ($session->cart->hasDomain()) {
					$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'You can complete the order checkout or delete one or more products and inserting a new one. Just click on the delete link in the cart summary.', 'status' => 'attention' ) );
				}
				$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $session->cart->getItems() ) ) );
			}
			
			$this->_helper->viewRenderer ( 'domain' );

		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
		
	}
	
	/**
	 * Update the cart quantity and totals
	 */
	public function updateAction(){
		$session = new Zend_Session_Namespace ( 'Default' );
			
		// Get all the params sent by the customer
		$cartitems = $this->getRequest ()->getParam ('cart');
		
		if(is_array($cartitems)){
			foreach($cartitems as $cartitem){
				if(is_numeric($cartitem['value']) && $cartitem['value'] > 0){
					$item = $session->cart->getItemByUid($cartitem['id']);
					$session->cart->updateItem($item, $cartitem['value']);
				}
			}
			$session->cart->update();
		}
		die(true);
	}

	/*
	 * Check the domain availability
	*/
	public function checkdomainAction() {
	
		try{
			
			$session = new Zend_Session_Namespace ( 'Default' );
			
			// Get all the params sent by the customer
			$params = $this->getRequest ()->getParams ();
		
			// redirect the customer to the contact form
			if ( (!empty($params ['mode']) && $params ['mode'] == "nothing")) {
				$this->_helper->redirector ( 'contacts' );
				return true;
			}
		
			if (empty ( $params ['domain'] ) || empty ( $params ['tlds'] )) {
				$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is a mandatory field. Choose a domain name.', 'status' => 'error' ) );
			}
		
			// Get the product (tld) selected by the customer
			$tldInfo = DomainsTlds::getAllInfo ( $params ['tlds'] );
		
			// Check if the parameter exists in our database
			if (isset ( $tldInfo ['tld_id'] )) {
		
				// If the owner of the domain wants to transfer the domain ...
				if (! empty ( $params ['mode'] ) && $params ['mode'] == "link") {
		
					if (! empty ( $tldInfo )) {

						// create the domain name
						$domain = trim(strtolower($params ['domain'])) . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
						
						// get the hosting item added in the cart
						$hostingItem = $session->cart->getHostingItem();
						
						// attach the domain name to the hosting plan
						if($hostingItem){
							$session->cart->attachDomain($hostingItem, $domain, $tldInfo['tld_id'], "transferDomain");
						}
						
						// redirect the customer to the contact form
						$this->_helper->redirector ( 'contacts' );
					}
						
				} else { // If the domain is still free and the customer needs to register it then ...
					
					$strDomain = $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
		
					// Check if the domain is still free
					$result = Domains::isAvailable ( $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'] );
		
					if ($result) { // If it is free
		
						// create the domain name
						$domain = trim(strtolower($params ['domain'])) . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
						
						// get the hosting item added in the cart
						$hostingItem = $session->cart->getHostingItem();
						
						// attach the domain name to the hosting plan
						if($hostingItem){
							$session->cart->attachDomain($hostingItem, $domain, $tldInfo['tld_id'], "registerDomain");
						}
		
						// Redirect the user to the
						$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'The domain is available for registration', 'status' => 'success' ) );
		
					} else {
						// If not redirect the customer to choose another name
						$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is not available for registration. Choose another domain name.', 'status' => 'error' ) );
					}
				}
			}

			$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is available for registration', 'status' => 'success' ) );

		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
	}
	
	/*
     * Show the logged reseller information
     */
	public function resellerAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		if (! isset ( $cart->products ) || count ( $cart->products ) == 0) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
		
		$request = $this->getRequest ();
		
		if (!empty($this->session->customer)) {
			$cart->reseller = $this->session->customer;
		} else {
			unset ( $cart->reseller );
			$this->_helper->redirector ( 'contacts', 'cart', 'default' );
		}
		
		// Create the sidebar if the cart has products
		if (! isset ( $cart->products )) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			$items = $cart->products;
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
		}
		
		// Create the item for the customers select object 
		$criteria = array (array ('where' => "parent_id = ?", 'params' => $cart->reseller ['customer_id'] ) );
		
		// Create the form
		$form = new Default_Form_ResellerForm ( array ('action' => "/cart/reseller", 'method' => 'post' ) );
		
		// Get the reseller information 
// 		$reseller = array ($cart->reseller ['customer_id'] => $cart->reseller ['firstname'] . " " . $cart->reseller ['lastname'] . " - " . $cart->reseller ['company'] );
		
		// Get the customers connected to the reseller
		$customers = Customers::getList ( false, $criteria );
		
		$form->getElement ( 'submit' )->setLabel ( 'Continue Order' );
		
		// Assign the customers to the select object
// 		$form->getElement ( 'customers' )->setMultiOptions ( $reseller + $customers );
		$this->view->form = $form;
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if ($form->isValid ( $params )) {
				$cart->contacts = Customers::getAllInfo ( $params ['customers'], "c.customer_id, a.address_id, cts.type_id, l.legalform_id, ct.country_id, cn.contact_id, s.status_id, c.*, a.*, l.*, cn.*, cts.*, s.*" );
				$this->_helper->redirector ( 'summary', 'cart', 'default' );
			}
		}
		
		$this->view->reseller = $cart->reseller;
		$this->_helper->viewRenderer ( 'reseller' );
	}
	

	/**
	 * Add a new domain within the cart
	 */
	public function newdomainAction(){
		$session = new Zend_Session_Namespace ( 'Default' );
		if (empty ( $session->cart ) || $session->cart->isEmpty()) {
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
		}
		
		$domain = $this->getRequest ()->getParam('domain');
		$tld = $this->getRequest ()->getParam('tld');
		
		
	}
	
	/*
     * Show the contact form
     */
	public function simplecontactsAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		unset ( $cart->reseller );
		
		if (! isset ( $cart->products ) || count ( $cart->products ) == 0) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			$items = $cart->products;
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
		}
		
		$request = $this->getRequest ();
		
		$form = new Default_Form_CartsimpleprofileForm ( array ('action' => "/cart/simplecontacts", 'method' => 'post' ) );
		
		// Fill the form if the user has already write his/her information
		if (isset ( $cart->contacts ) && is_array ( $cart->contacts )) {
			$form->populate ( $cart->contacts );
		}
		
		$form->getElement ( 'save' )->setLabel ( 'Continue Order' );
		$this->view->form = $form;
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if ($form->isValid ( $params )) {
				
				$params = $request->getPost ();
				$cart->contacts = $params;
				
				// Check if there is a domain service within the cart.
				// If a domain is present we have to create a nic-handle in order to register the 
				// customer in the remote registrar database
				$hasdomain = $this->hasDomain ();
				
				// Create a customer or get his ID
				$customerid = $this->CreateCustomer ( $params, $hasdomain );
				
				// Do the login
				$this->doLogin ( $customerid );
				
				$this->_helper->redirector ( 'summary', 'cart', 'default', array ('mex' => 'Well done! Now you have to choose your preferite payment method.', 'status' => 'success' ) );
			}
		}
		
	}
	
	/*
     * Review the cart before the payment action
     */
	public function summaryAction() {
		$session = new Zend_Session_Namespace ( 'Default' );
		$request = $this->getRequest ();
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
		
		if (empty ( $session->cart ) || $session->cart->isEmpty()) {
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
		}
		
		if (!$session->cart->getCustomer ()) {
			$this->_helper->redirector ( 'contacts' );
		}
		
		// Get the heading of the cart
		$cart_heading = $session->cart->getHeading();
		
		// Check if the user is VAT free
		$this->view->isVATFree	= Customers::isVATFree($cart_heading['customer']['id']);
		
		// Update the cart totals
		$session->cart->update();
		
		// Send the cart information to the view
		$this->view->lastproduct = $session->lastproduct;
		$this->view->cart = $session->cart;
	}

	/**
	 * Payment gateway page
	 */
	public function paymentAction() {
		$session = new Zend_Session_Namespace ( 'Default' );
		
		if (empty ( $session->cart ) || $session->cart->isEmpty()) {
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
		}
		
		// get the bank payment gateway
		$gateways = Banks::findAllActive ( "name, description, classname", true );
		
		$form = new Default_Form_PaymentForm ( array ('action' => '/cart/redirect', 'method' => 'post' ) );
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
		
		$this->view->gateways = $gateways;
		$this->view->form = $form;
	}
	
	/**
	 * Render the method of payment gateway form
	 */
	public function redirectAction() {
		$session = new Zend_Session_Namespace ( 'Default' );
		
		if (empty ( $session->cart ) || $session->cart->isEmpty()) {
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
		}
		
		// get the payment method selected
		$payment = $this->getRequest()->getParam('payment');
		
		// get the payment gateway information
		if(!empty($payment)){
			$gateway = Banks::getAllInfo($payment, "*", true);
			
			// check if the payment gateway exists
			if (! empty ( $gateway[0] ['classname'] ) && class_exists ( $gateway [0]['classname'] )) {
				
				// update the cart
				$session->cart->update();
	
				// create the order
				$order = $session->cart->createOrder();
				
				if($order){

					if($this->getRequest()->getParam('note')){
						Messages::addMessage($this->getRequest()->getParam('note'), $session->cart->getCustomerId(), null, $session->cart->getOrderid());
					}
					
					// clear the cart
					$session->cart->clearAll();
					
					$class = $gateway[0] ['classname'];
					$payment = new $class ( $order->order_id );
					
					// create the form payment gateway
					$form = $payment->setUrlOk ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $gateway [0]['classname'] ) )
									->setUrlKo ( $_SERVER ['HTTP_HOST'] . "/orders/response/" . md5 ( $gateway[0] ['classname'] ) )
									->setUrlCallback ( $_SERVER ['HTTP_HOST'] . "/common/callback/gateway/" . md5 ( $gateway[0] ['classname'] )  )
									#->setRedirect(true)
									->setFormHidden(true)
									->CreateForm ();
					
					// push the payment gateway html form in the view
					$this->view->name = $form['name'];
					$this->view->html = $form['html'];
				}else{
					$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "The order has been not created, please contact the administrator") );
				}
			}
		}
	}

	/*
	 * Review the cart before the payment action
	*/
	public function checkoutAction() {
		$session = new Zend_Session_Namespace ( 'Default' );
			
		$request = $this->getRequest ();
		
		if (empty ( $session->cart ) || $session->cart->isEmpty()) {
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
		}
		
		// If the order has not been created yet ... 
		if(!$session->cart->getOrderid()){
			$session->cart->createOrder();  // Create the order
		}

		// Send the cart details to the view
		$this->view->orderid = $session->cart->getOrderid();
		
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	/**
     * Delete a product or domain from the cart list 
     */
	public function deleteAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		// Get the parameters
		$params = $request = $this->getRequest ()->getParams ();
		
		// Get all the cart products
		$products = $cart->products;
		
		$index = 0;
		
		// If the product is a domain delete the temporary session domain information
		if (! empty ( $params ['tld'] )) {
			foreach ( $products as $key => $product ) {
			    #Delete domain in hosting if is incluse
                if (! empty ( $product ['domain_selected'] ) && $product ['domain_selected'] == $params ['tld']) {
					unset ( $products [$index] );  // Delete the product from the session cart 
					$cart->products = array_values ( $products );
					unset ( $cart->domain );
				} elseif( $products['type'] == 'hosting' && $products['site'] == $params ['tld']) {
                   unset( $cart->products[$key]['site'] ); 
                }
                
				$index ++;
			}
		}
		
		// Check if the product selected is a common product 
		if (! empty ( $params ['product'] ) && is_numeric ( $params ['product'] )) {
			
			// Cycle all the products inserted in the cart
			foreach ( $products as $product ) {
				
				// Matching of the product selected and the product cycled
				if ($product ['product_id'] == $params ['product']) {
					
					// Delete the product from the session cart 
					unset ( $products [$index] );
					$cart->products = array_values ( $products );
					break;
				}
				$index ++;
			}
		} elseif (! empty ( $params ['product'] ) && $params ['product'] == "all") {
			unset ( $cart );
		}
		
		if (! empty ( $cart->products ) && count ( $cart->products ) > 0) {
			if ($this->checkIfHostingProductIsPresentWithinCart ()) {
				$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain has been deleted from the cart list. Choose another domain name.', 'status' => 'success' ) );
			} else {
				$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'The domain has been deleted from the cart list.', 'status' => 'success' ) );
			}
		} else {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
	
	}
	
	/**
	 * Do the login action
	 * 
	 * @param integer $customerid
	 */
	private function doLogin($customerid) {
		
		if(is_numeric($customerid)){
			$session = new Zend_Session_Namespace ( 'Default' );
			$auth = Zend_Auth::getInstance ();
			$auth->setStorage ( new Zend_Auth_Storage_Session ( 'default' ) );
			
			$result = new Zend_Auth_Result ( Zend_Auth_Result::SUCCESS, null );
			$customer = Customers::getAllInfo ( $customerid, "c.customer_id, a.address_id, cts.type_id, l.legalform_id, ct.country_id, cn.contact_id, s.status_id, c.*, a.*, l.*, cn.*, cts.*, s.*" );
			
			// We're authenticated!
			$auth->getStorage ()->write ( $customer );
			
			// Set the owner of the cart
			$session->cart->setCustomer($customerid);
			
			return $customer;
		}
		
		return false;
	}
	
	/**
	 * Create a customer using the cart information and it creates 
	 * the Nic Handle in order to communicate with the default registrars 
	 */
	private function CreateCustomer($params, $createNicHandle = false) {
		
		$params ['contacttypes'] = 1; // Telephone
		$params ['contact'] = $params ['telephone'];
		
		$customerID = Customers::Create ( $params );

		return $customerID;
	}
	
	/**
     * Delete the cart session
     */
	public function deletesessionAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		if (is_numeric ( $cart->contacts ['customer_id'] )) {
			Customers::del ( $cart->contacts ['customer_id'] );
		}
		unset ( $cart );
		$this->_helper->redirector ( 'out', 'index', 'default' );
	}
	
	/**
	 * Get the company type for the dropdown select box
	 */
	public function getcompanytypesAction() {
		// Get the parameters
		$id = $request = $this->getRequest ()->getParam ( 'id' );
		if (! empty ( $id ) && is_numeric ( $id )) {
			echo json_encode ( CompanyTypes::getListbyLegalformID ( $id ) );
		}
		die ();
	}
}